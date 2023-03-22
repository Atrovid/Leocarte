#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>

#include <EEPROM.h>

#include <WiFiClient.h>

#include "wifimanager.h"


#include <SPI.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

#include <SoftwareSerial.h>
#include <PN532_SWHSU.h>
#include <PN532.h>



// Create AsyncWebServer object on port 80
ESP8266WebServer server(80);

// Search for parameter in HTTP POST request
const char* PARAM_INPUT_1 = "ssid";
const char* PARAM_INPUT_2 = "pass";
const char* PARAM_INPUT_5 = "serverName";

//Variables to save values from HTML form
String ssid;
String pass;
String serverName;


// the following variables are unsigned longs because the time, measured in
// milliseconds, will quickly become a bigger number than can be stored in an int.
unsigned long lastTime = 0;
// Timer set to 10 minutes (600000)
//unsigned long timerDelay = 600000;
// Set timer to 5 seconds (5000)
unsigned long timerDelay = 5000;
// Timer variables
unsigned long previousMillis = 0;
const long interval = 10000;  // interval to wait for Wi-Fi connection (milliseconds)

// Set LED GPIO
const int ledPin = 2;
// Stores LED state

String ledState;
bool connected = false;

boolean restart = false;



#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 32 // OLED display height, in pixels


SoftwareSerial SWSerial( D6, D5 ); // RX (SDA), TX (SCL)

PN532_SWHSU pn532swhsu( SWSerial );
PN532 nfc( pn532swhsu );

#define OLED_RESET     -1 // Reset pin # (or -1 if sharing Arduino reset pin)
#define SCREEN_ADDRESS 0x3C ///< See datasheet for Address; 0x3D for 128x64, 0x3C for 128x32

Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);


int clearCounter = 0;
bool screenCleared = true;
  


int writeEEPROM(int addr, String str){
  int i;              
  for (i = 0; i < str.length(); i++){
    EEPROM.write(addr+i, str[i]);
  }
  EEPROM.write(addr+i, '\0');
  EEPROM.commit();
  return addr+i;
}

String readEEPROM(int addr, int length){
  String str = "";
  char c;
  for (int i = 0; i <length; i++){
    c = char(EEPROM.read(addr+i));
    if (c=='\0'){
      break;
    }
    str += c;
  }
  return str;
}


// Initialize WiFi
bool initWiFi() {
  if(ssid==""){
    Serial.println("Undefined SSID.");
    return false;
  }
  WiFi.mode(WIFI_STA);

  WiFi.begin(ssid.c_str(), pass.c_str());

  Serial.println("Connecting to WiFi...");
  delay(10000);
  if(WiFi.status() != WL_CONNECTED) {
    Serial.println("Failed to connect.");
    return false;
  }

  Serial.println(WiFi.localIP());
  return true;
}


void connectionManager() {
  server.send(200, "text/html", wifimanager_html);
}


void saveServerArgs() {
  String ssid = server.arg(PARAM_INPUT_1);
  Serial.print("SSID set to: ");
  Serial.println(ssid);
  // Write file to save value
  writeEEPROM(0, ssid);
  
  String pass = server.arg(PARAM_INPUT_2);
  Serial.print("Password set to: ");
  Serial.println(pass);
  // Write file to save value
  writeEEPROM(100, pass);
  
  String serverName = server.arg(PARAM_INPUT_5);    
  Serial.print("serverName set to: ");
  Serial.println(serverName);
  // Write file to save value
  writeEEPROM(400, serverName);
  restart = true;
  server.send(200, "text/plain", "Done. ESP will restart");
  
}

void setup() {
  Serial.begin(115200);
  while (!Serial);
  EEPROM.begin(512);

  // Set GPIO 2 as an OUTPUT
  pinMode(ledPin, OUTPUT);
  digitalWrite(ledPin, LOW);
  
  // Load values saved in EEPROM
  ssid = readEEPROM(0, 100);
  pass = readEEPROM(100, 100);
  serverName = readEEPROM(400, 100);

  Serial.println("Network information:");
  Serial.println(ssid);
  Serial.println(pass);
  Serial.println(serverName);


  if(initWiFi()) {
    connected = true;
        
    if(!display.begin(SSD1306_SWITCHCAPVCC, SCREEN_ADDRESS)) {
      Serial.println(F("SSD1306 allocation failed"));
      for(;;); 
    }
    drawMessage("welcome", "");    

    nfc.begin();
    
    uint32_t versiondata = nfc.getFirmwareVersion();
    if (! versiondata) {
      Serial.print("Didn't find PN53x board");
      while (1); // halt
    }
    
    // Got ok data, print it out!
    Serial.print("Found chip PN5"); Serial.println((versiondata>>24) & 0xFF, HEX); 
    Serial.print("Firmware ver. "); Serial.print((versiondata>>16) & 0xFF, DEC); 
    Serial.print('.'); Serial.println((versiondata>>8) & 0xFF, DEC);
    
    // Set the max number of retry attempts to read from a card
    // This prevents us from waiting forever for a card, which is
    // the default behaviour of the PN532.
    nfc.setPassiveActivationRetries(0xFF);
    
    // configure board to read RFID tags
    nfc.SAMConfig();
      
    Serial.println("Waiting for an ISO14443A card");
    drawMessage("wifi", "connected");    
    delay(1200);
    display.clearDisplay();
    display.display();    
  } else {
    Serial.println("Setting AP (Access Point)");

    WiFi.softAP("ESP-WIFI-MANAGER", NULL);

    IPAddress IP = WiFi.softAPIP();
    Serial.print("AP IP address: ");
    Serial.println(IP); 

    // Web Server Root URL
    server.on("/", HTTP_GET, connectionManager);
    
    
    server.on("/", HTTP_POST, saveServerArgs);
    server.begin();
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
      String name = sendAttendance(csn);
      drawMessage("CSN",csn);

      screenCleared = false;
      clearCounter = 0;
    }
  } else {
    server.handleClient();
  }

}

String readCSN(){
  boolean success;
    
  uint8_t uid[] = { 0, 0, 0, 0, 0, 0, 0 };  // Buffer to store the returned UID
  uint8_t uidLength;                        // Length of the UID (4 or 7 bytes depending on ISO14443A card type)
  String csn = "";

  // Wait for an ISO14443A type cards (Mifare, etc.).  When one is found
  // 'uid' will be populated with the UID, and uidLength will indicate
  // if the uid is 4 bytes (Mifare Classic) or 7 bytes (Mifare Ultralight)
  success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, &uid[0], &uidLength);
  
  if (success) {
    Serial.println("Found a card!");
    Serial.print("UID Length: ");Serial.print(uidLength, DEC);Serial.println(" bytes");
    Serial.print("UID Value: ");
    char myHex[4] = "";
    for (uint8_t i=0; i < uidLength; i++) 
    {
      Serial.print(" ");
      Serial.print(uid[i], HEX); 
      ltoa(uid[i],myHex,16); //convert to c string base 16
      csn += String(myHex);
    }
    Serial.println("");
    // Wait 1 second before continuing
    delay(1000);
  }
  else {
    // PN532 probably timed out waiting for a card
    Serial.println("Timed out waiting for a card");    
  }
  return csn;
}


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


String sendAttendance(String csn){
  
    if ((millis() - lastTime) > timerDelay) {
    //Check WiFi connection status
      if(WiFi.status()== WL_CONNECTED){
        WiFiClient client;
        HTTPClient http;

        String serverPath = serverName;
        
        // Your Domain name with URL path or IP address with path
        http.begin(client, serverPath.c_str());
    
        // If you need Node-RED/server authentication, insert user and password below
        //http.setAuthorization("REPLACE_WITH_SERVER_USERNAME", "REPLACE_WITH_SERVER_PASSWORD");
          
        // Send HTTP GET request
        int httpResponseCode = http.GET();
        
        if (httpResponseCode>0) {
          Serial.print("HTTP Response code: ");
          Serial.println(httpResponseCode);
          String payload = http.getString();
          Serial.println(payload);
          return payload;
        }
        else {
          Serial.print("Error code: ");
          Serial.println(httpResponseCode);
        }
        // Free resources
        http.end();
      }
      else {
        Serial.println("WiFi Disconnected");
      }
      lastTime = millis();
    }
    return "";
}