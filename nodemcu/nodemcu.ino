#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>


const char* ssid = "Galang";
const char* password = "chesagal7629";


#define LED_PIN 15
#define BTN_PIN 5
#define RST_PIN 0   
#define SS_PIN 2  
#define BUZZER_PIN 4

MFRC522 mfrc522(SS_PIN, RST_PIN); 

void setup() {
  Serial.begin(9600);
  WiFi.hostname("NodeMcu");
  WiFi.begin(ssid, password);


  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi konek");
  Serial.println("Alamat IP:");
  Serial.println(WiFi.localIP());

//konfigurasi
  pinMode(LED_PIN, OUTPUT);
  pinMode(BTN_PIN, INPUT_PULLUP);
  pinMode(BUZZER_PIN, OUTPUT);

  SPI.begin();
  mfrc522.PCD_Init();

  Serial.println("tempelkan kartu"); 
  Serial.println();
}

void loop() {

  if (digitalRead(BTN_PIN) == LOW) {
    digitalWrite(LED_PIN, HIGH);
    delay(50); 

    while (digitalRead(BTN_PIN) == LOW); 
      delay(50);

      String Link = "http://192.168.1.12/absensi/admin/ubahmode.php";
      WiFiClient client;
      HTTPClient http;
      http.begin(client, Link);
  
      int httpCode = http.GET();
      if (httpCode > 0) {
        String payload = http.getString();
        Serial.println(payload);
      } else {
        Serial.print("Error pada HTTP request, code: ");
        Serial.println(httpCode);
      }
      http.end();
    }

  digitalWrite(LED_PIN, LOW);


  if (!mfrc522.PICC_IsNewCardPresent()) return;
  if (!mfrc522.PICC_ReadCardSerial()) return;


  String IDTAG = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    IDTAG += String(mfrc522.uid.uidByte[i]);
  }

  digitalWrite(LED_PIN, HIGH);

  const char* host = "192.168.1.12";
  const int httpPort = 80;
  WiFiClient client;

  Serial.println("Menghubungkan ke server...");

  if (!client.connect(host, httpPort)) {
    Serial.println("Koneksi gagal");
    return;
  }

  String Link = "http://192.168.1.12/absensi/admin/kirimkartu.php?nokartu=" + IDTAG;
  HTTPClient http;
  http.begin(client, Link);

  int httpCode = http.GET();
  if (httpCode > 0) {
    String payload = http.getString();
    Serial.println(payload);
    digitalWrite(BUZZER_PIN, HIGH);
    delay(500);
    digitalWrite(BUZZER_PIN, LOW);
  } else {
    Serial.print("Error saat kirim kartu, code: ");
    Serial.println(httpCode);
  }

  http.end();
  delay(2000); 
}