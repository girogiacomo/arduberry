// rf22_client.pde
// -*- mode: C++ -*-
// Example sketch showing how to create a simple messageing client
// with the RH_RF22 class. RH_RF22 class does not provide for addressing or
// reliability, so you should only use RH_RF22 if you do not need the higher
// level messaging abilities.
// It is designed to work with the other example rf22_server
// Tested on Duemilanove, Uno with Sparkfun RFM22 wireless shield
// Tested on Flymaple with sparkfun RFM22 wireless shield
// Tested on ChiKit Uno32 with sparkfun RFM22 wireless shield
// MAX 49 caratteri

#include <SPI.h>
#include <RH_RF22.h>

// Singleton instance of the radio driver
RH_RF22 rf22;
float freq = 434;
float delta = 0.05;
int index=13;
String key = "atxZ47CG";
void setup() {
  Serial.begin(9600);
  if (!rf22.init())
    Serial.println("init failed");
  // Defaults after init are 434.0MHz, 0.05MHz AFC pull-in, modulation FSK_Rb2_4Fd36
  //rf22.setFrequency(freq, delta);
  rf22.setModemConfig(index);
}

void loop(){
  if(Serial.available()){
      String dati = key + Serial.readString();
      Serial.println("Inviato:  " + dati);
      char data[dati.length()+1+8];
      dati.toCharArray(data, dati.length()+1+8);
      rf22.send(data, sizeof(data));
      
      rf22.waitPacketSent();
      
      delay(150);
  }
  if (rf22.available()){
    uint8_t buf[RH_RF22_MAX_MESSAGE_LEN];
    uint8_t len = sizeof(buf);
    if (rf22.recv(buf, &len)){
      bool flag = HIGH;   
      String msg = (char*)buf;
      Serial.println("Ricevuto: " + msg);
   }
  }
}

