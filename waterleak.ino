// ESP32 + water-leak sensor (GPIO 34) + YF-S201 flow sensor (GPIO 27). Posts to api/ingest.php.
#include <WiFi.h>
#include <HTTPClient.h>
const char* SSID="YOUR_WIFI"; const char* PASS="YOUR_PASSWORD";
const char* URL="http://YOUR_SERVER/waterleak/api/ingest.php"; const char* KEY="YOUR_DEVICE_KEY";
volatile unsigned long pulses=0; void IRAM_ATTR onPulse(){pulses++;}
void send(const char* label,const char* type,float v){
  for(int t=0;t<3;t++){ // retry if the internet is down
    HTTPClient h; h.begin(URL); h.addHeader("Content-Type","application/json");
    String body=String("{\"device_key\":\"")+KEY+"\",\"label\":\""+label+"\",\"type\":\""+type+"\",\"value\":"+v+"}";
    int c=h.POST(body); h.end(); if(c==200) return; delay(2000);
  }
}
void setup(){ pinMode(34,INPUT); pinMode(27,INPUT_PULLUP); attachInterrupt(27,onPulse,FALLING);
  WiFi.begin(SSID,PASS); while(WiFi.status()!=WL_CONNECTED) delay(500); }
void loop(){
  pulses=0; delay(60000);                       // measure for one minute
  float litres=pulses/450.0;                    // YF-S201 approx. 450 pulses per litre
  send("Main pipe","flow",litres);
  send("Kitchen sink","leak",analogRead(34)>1500?1:0);
}
