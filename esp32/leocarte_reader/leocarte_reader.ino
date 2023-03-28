// For the PN532 and the LCD screen
#include <SPI.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <SoftwareSerial.h>
#include <PN532_SWHSU.h>
#include <PN532.h>


#include "esp_wpa2.h"
#include "esp_wifi.h"
#include "esp_wpa2.h"

#include <WiFiClientSecure.h>
#include <WiFiClient.h>
#include <HTTPClient.h>


#include "config.h"

#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 32 // OLED display height, in pixels

#define HTTPS_PORT 80


SoftwareSerial SWSerial(D3, D2); // SDA, SCL

PN532_SWHSU pn532swhsu(SWSerial);
PN532 nfc(pn532swhsu);

#define OLED_RESET -1       // Reset pin # (or -1 if sharing Arduino reset pin)
#define SCREEN_ADDRESS 0x3C ///< See datasheet for Address; 0x3D for 128x64, 0x3C for 128x32

Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

int clearCounter = 0;
bool screenCleared = true;

bool connected = false;

boolean restart = false;


String url = "?action=attendance";

WiFiClient client;




void setup() {
  Serial.begin(115200);
  delay(10);

  Serial.println("Hello World !");  

  if( initWifi() ) {
    connected = true;   
    if(!display.begin(SSD1306_SWITCHCAPVCC, SCREEN_ADDRESS)) {
      Serial.println(F("SSD1306 allocation failed"));
      for(;;); 
    }
    drawMessage("welcome", "");
    nfc.begin();

    uint32_t versiondata = nfc.getFirmwareVersion();
    while (! versiondata) {
      Serial.print("Didn't find PN53x board");
    }
    Serial.print("Found chip PN5"); Serial.println((versiondata>>24) & 0xFF, HEX); 
    Serial.print("Firmware ver. "); Serial.print((versiondata>>16) & 0xFF, DEC); 
    Serial.print('.'); Serial.println((versiondata>>8) & 0xFF, DEC);

    nfc.setPassiveActivationRetries(0xFF);


    nfc.SAMConfig();

    Serial.println("Waiting for an ISO14443A card");
    drawMessage("NFC reader", "connected");    
    delay(1200);
    display.clearDisplay();
    display.display();
     
  } else {
    Serial.println("Wifi connection failed.");
    ESP.restart();
  }
}


void loop() {  
    if (restart){
        delay(5000);
        ESP.restart();
    }
    
    if (connected){
        if (!screenCleared){
        clearCounter ++; 
        }
        if (clearCounter>3){
        clearCounter = 0;
        screenCleared = true;
        display.clearDisplay();  
        display.display();
        }
        
        String csn = readCSN();
        if (csn != ""){
          String name = sendAttendance(csn, room);
          
          drawMessage(getValue(name, '/', 0),getValue(name, '/', 1));

          screenCleared = false;
          clearCounter = 0;
        }
    }

}


String sendAttendance(String csn, String room){
    String payload;
    HTTPClient http;
    Serial.println("Connecting to website: ");
    String co = protocol + "://"+ host +"/" + url + "&csn=" + csn + "&room="+room;
    
    http.begin(co); 
    

    int httpCode = http.GET();
    if(httpCode > 0) {
      Serial.printf("[HTTP] GET... code: %d\n", httpCode);
      //file found at server --> on unsucessful connection code will be -1
      if(httpCode == HTTP_CODE_OK) {
        payload = http.getString();
        Serial.println(payload);
      }
     }else{
      Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
      }
    http.end(); 
    
    return payload;
}











bool initWifi() {
  
  WiFi.disconnect(true);
  WiFi.mode(WIFI_STA);

  ESP_LOGI(TAG, "Setting WiFi configuration SSID %s...", wifi_config.sta.ssid);
  ESP_ERROR_CHECK( esp_wifi_set_mode(WIFI_MODE_STA) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_set_identity((uint8_t *)ID, strlen(ID)) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_set_username((uint8_t *)USERNAME, strlen(USERNAME)) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_set_password((uint8_t *)PASSWORD, strlen(PASSWORD)) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_enable() );
  WiFi.begin(SSID);
  

  if(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println(WiFi.localIP());
  return true;
}


String readCSN()
{
    boolean success;

    uint8_t uid[] = {0, 0, 0, 0, 0, 0, 0}; // Buffer to store the returned UID
    uint8_t uidLength;                     // Length of the UID (4 or 7 bytes depending on ISO14443A card type)
    String csn = "";

    // Wait for an ISO14443A type cards (Mifare, etc.).
    success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, &uid[0], &uidLength);

    if (success)
    {
        Serial.println("Found a card!");
        Serial.print("UID Length: ");
        Serial.print(uidLength, DEC);
        Serial.println(" bytes");
        Serial.print("UID Value: ");
        char myHex[4] = "";
        for (uint8_t i = 0; i < uidLength; i++)
        {
            Serial.print(" ");
            Serial.print(uid[i], HEX);
            ltoa(uid[i], myHex, 16); // convert to c string base 16
            sprintf(myHex, "%02x", uid[i]);
            csn += String(myHex);
        }
        Serial.println("");
        Serial.println(csn);
    }
    else
    {
        // PN532 probably timed out waiting for a card
        Serial.println("Timed out waiting for a card");
        delay(100);
    }
    return csn;
}

// Draws on the LCD screen
void drawMessage(String name, String surname)
{
    display.clearDisplay();
    int size = 2;
    display.setTextSize(size);
    display.setTextColor(SSD1306_WHITE);
    display.setCursor(0, 0);
    display.println(name);
    if (surname.length() > 12)
    {
        size = 1;
    }
    display.setTextSize(size);
    display.println(surname);
    display.display();
}

String getValue(String data, char separator, int index)
{
  int found = 0;
  int strIndex[] = {0, -1};
  int maxIndex = data.length()-1;

  for(int i=0; i<=maxIndex && found<=index; i++){
    if(data.charAt(i)==separator || i==maxIndex){
        found++;
        strIndex[0] = strIndex[1]+1;
        strIndex[1] = (i == maxIndex) ? i+1 : i;
    }
  }

  return found>index ? data.substring(strIndex[0], strIndex[1]) : "";
}

