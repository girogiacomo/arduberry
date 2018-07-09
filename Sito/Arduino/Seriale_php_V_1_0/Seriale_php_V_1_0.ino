#include <avr/io.h>
#include <avr/wdt.h>

#define Reset_AVR() wdt_enable(WDTO_30MS); while(1) {} 

void setup() {
  for(int i=2; i<14; i++)
    pinMode(i, OUTPUT);

  Serial.begin(9600);
}

boolean pin[14]={0,0,0,0,0,0,0,0,0,0,0,0,0,0};
int pmax=13, pmin=2, tmp;

int rs() {                                                                  // Lettura seriale (procedura)
  delay(6);
  int serialtemp=0;
  for (int ser=Serial.available(); ser>0; ser--)
    serialtemp=serialtemp+(Serial.read()-48)*pow(10, ser-1);
  Serial.flush();
  return serialtemp;
}

void loop() {

if (Serial.available())
      switch (Serial.read()) { 
        case 97:                                      // accensione
               tmp=rs();
              if (tmp>=pmin && tmp<=pmax){
                  digitalWrite(tmp, HIGH); 
                  pin[tmp]=true;
               }
          break;
        case 115:                                     // spegnimento
              tmp=rs();
               if (tmp>=pmin && tmp<=pmax){
                  digitalWrite(tmp, LOW); 
                  pin[tmp]=false;
               }
          break;
        case 63:                                     // stato
              for(int i=0; i<14; i++){
                if(pin[i]==true){
                  Serial.print(i);
                  Serial.print("|");
                }
              }
          break;
       case 114:
              Reset_AVR();
          break;
        default:
          break;
        
      }

}

