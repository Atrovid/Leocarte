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

#include <EEPROM.h>

#include <WiFiClient.h>

#include "login.h"

#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 32 // OLED display height, in pixels

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




#define EXAMPLE_EAP_ID "matheus_garbelini"



void setup() {
  Serial.begin(115200);
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
    Serial.println("problem wifi connection ....");
    ESP.restart();
  }
}













bool initWifi() {
  
  WiFi.disconnect(true);
  WiFi.mode(WIFI_STA);
  esp_wpa2_config_t config = WPA2_CONFIG_INIT_DEFAULT();

  ESP_LOGI(TAG, "Setting WiFi configuration SSID %s...", wifi_config.sta.ssid);
  ESP_ERROR_CHECK( esp_wifi_set_mode(WIFI_MODE_STA) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_set_identity((uint8_t *)ID, strlen(ID)) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_set_username((uint8_t *)USERNAME, strlen(USERNAME)) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_set_password((uint8_t *)PASSWORD, strlen(PASSWORD)) );
  ESP_ERROR_CHECK( esp_wifi_sta_wpa2_ent_enable(&config) );
  WiFi.begin(SSID);
  
  if(WiFi.status() != WL_CONNECTED) {
    Serial.println("Failed to connect.");
    return false;
  }
  Serial.println(WiFi.localIP());
  return true
  
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
            csn += String(myHex);
        }
        Serial.println("");
    }
    else
    {
        // PN532 probably timed out waiting for a card
        Serial.println("Timed out waiting for a card");
    }
    return csn;
}





// Draws on the LCD screen
void drawMessage(String name, String surname) {
    display.clearDisplay();
    int size = 2;
    display.setTextSize(size);
    display.setTextColor(SSD1306_WHITE);      
    display.setCursor(0,0);            
    display.println(name);
    if ( surname.length() > 12) {
        size = 1;
    }  
    display.setTextSize(size);  
    display.println(surname); 
    display.display();
}




