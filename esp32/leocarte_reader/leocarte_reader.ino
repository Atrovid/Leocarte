// For the PN532 and the LCD screen
#include <SPI.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <SoftwareSerial.h>
#include <PN532_SWHSU.h>
#include <PN532.h>

#include <EEPROM.h>

#include <WiFiClient.h>

// HTML page for managing connection the network connection
#include "wifimanager.h"

#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 32 // OLED display height, in pixels

#define HTTPS_PORT 443


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

WiFiClientSecure client;





String sendAttendance(String csn, String room){
    Serial.print("Connecting to website: ");
    Serial.println(host);
    if (client.connect(host, HTTPS_PORT)) {
        client.print(String("GET ") + url + "&csn=" + csn + "&room=" + room + " HTTP/1.1\r\n" + "Host: " + host + "\r\n" + "User-Agent: ESP32\r\n" + "Connection: close\r\n\r\n");
        while (client.connected()) {
            String header = client.readStringUntil('\n');
            Serial.println(header);
            if (header == "\r") {
                break;
            }
        }
        String line = client.readStringUntil('\n');
        Serial.println(line);
    } else {
        Serial.println("Connection unsucessful");
    }
    return line;
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
