#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>


const char* ssid = "Galang";
const char* password = "chesagal7629";
const char* host = "192.168.1.7";

#define LED_PIN 15
#define BTN_PIN 5

#define SDA_PIN 2
#define RST_PIN 0

MFRC522 mfrc522(SDA_PIN, RST_PIN);

void setup() {
  Serial.begin(9600);
  WiFi.hostname("NodeMcu");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("Wifi konek");
  Serial.println("Wifi Address:");
  Serial.println(WiFi.localIP());

  pinMode(LED_PIN, OUTPUT);
  pinMode(BTN_PIN, INPUT_PULLUP);

  SPI.begin();
  mfrc522.PCD_Init();
  Serial.println("Tempelkan kartu");
}

void loop() {
  if (digitalRead(BTN_PIN) == LOW) {  
    digitalWrite(LED_PIN, HIGH);
    
    // Bagian untuk mengirim GET request ke ubahmode.php
    String url1 = "http://192.168.1.7/absensi/admin/ubahmode.php";
    WiFiClient client; 
    HTTPClient http;
    http.begin(client, url1); 

    while (digitalRead(BTN_PIN) == LOW) {
      int httpCode = http.GET();
      String payload = http.getString();
      Serial.println(payload);
      delay(1000);
    }
    
    http.end();
    digitalWrite(LED_PIN, LOW);

    // Mengecek kehadiran kartu
    if (!mfrc522.PICC_IsNewCardPresent())
      return;
    if (!mfrc522.PICC_ReadCardSerial())
      return;

    // Mengambil UID dari kartu
    String IDTAG = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      // Konversi ke format HEX agar lebih mudah dibaca
      IDTAG += String(mfrc522.uid.uidByte[i], HEX);
    }
    
    digitalWrite(LED_PIN, HIGH);

    // Bagian untuk mengirim GET request dengan parameter UID
    WiFiClient client2;
    const int httpPort = 80;
    if (!client2.connect(host, httpPort)) {
      Serial.println("koneksi gagal");
      return;
    }

    String url2 = "http://192.168.1.7/absensi/admin/kirimkartu.php?nokartu=" + IDTAG;
    HTTPClient http2;
    http2.begin(client2, url2);
    int httpCode = http2.GET();
    String payload = http2.getString();
    Serial.println(payload);
    http2.end();

    delay(2000);
    digitalWrite(LED_PIN, LOW);
  }
}
