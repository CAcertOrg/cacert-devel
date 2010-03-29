#include <stdio.h>
#include <ctype.h>
#include <stdlib.h>
#include <string.h>
typedef unsigned char uchar;
typedef struct{char * nm; int v;} vp;
vp vpl[] = {
  {"nbsp", 160}, {"lt",0x3c}, {"amp", 38},
  {"eacute", 233}, {"egrave", 232}, {"ouml", 246},
  {"alpha", 0x3b1}, {"beta", 0x3b2}, {"gamma", 0x3b3},
  {"delta", 0x3b4}, {"Delta", 0x394},
  {"sigma", 0x3c3}, {"Sigma", 0x3a3},
  {"epsilon", 0x3b5}, {"zeta", 0x3b6},
  {"theta", 0x3b8}, {"mu", 0x3bc},
  {"phi", 0x3c6},
  {"omega", 0x3c9},
  {"lambda", 0x3bb}, {"rho", 0x3c1},
  {"pi", 0x3c0}, {"Pi", 0x3a0},
  {"ndash", 0x2013}, {"mdash", 0x2014},
  {"and", 8743}, {"rarr", 8594}, {"forall", 0x2200},
  {"sum", 8721}};
int cc = 0; // count of conversions.
static void Utf(int m, uint a){
    if (a & m) {Utf(m>>1, a>>6); putchar(128 | a & 63);}
    else putchar((m<<1)&255 | a);}
static void utf8(uint a){
  if(a == '<') printf("%s", "&lt;");
  else if(a == '&') printf("%s", "&amp;");
  else if(a & -128) {++cc;
  Utf(-32, a>>6); putchar(128 | a & 63);} else putchar(a);}
char * em[] = {"", "tag", "quoted string", "utf", "character ref"};
int lc = 1, cil = 0, tcc=0;
char gc(int x){char c = getchar();
  if(c == EOF && feof(stdin)) {
     if(x) fprintf(stderr, "file ended in %s\n", em[x]);
     fprintf(stderr, "Converted %d characters\n", cc);
     exit(0);}
  if(c == 10 || c == 13) {tcc += cil; cil = 0; ++lc;}
  ++cil; return c;}
void loc(){fprintf(stderr, "Ending at byte %d of line %d,"
    "(or 0x%x in file):\n", cil, lc, tcc+cil);}
char gx(){char c = gc(3); if ((c&0xc0) != 0x80)
   {loc(); fprintf(stderr, "Bad utf8 extension byte: %02X\n", c);}
   return c;}
int main(int argc, char * * args){
  int bk = argc == 2;
  while(1){
  int vx(int x){if((x & 0xffffffe0) == 0x80){
     if(x == 150) return 8211;
     if(x == 151) return 8212;
     loc(); fprintf(stderr, "Invalid character: 0x%x=%d\n", x, x);}
     return x;}
  uchar c = gc(0);
  if(c == '<'){putchar(c); while(1){char c = gc(1);
     if(c == '"'){putchar(c); while(1){char c = gc(2);
        if(c == '"'){putchar(c); break;}
        else putchar(c);}}
     else if(c == '>'){putchar(c); break;}
     else putchar(c);}}
  else if(bk && c > 127){int v=0, sc=0, C=c;  
     while(C&0x40){C <<=1; v = (v<<6) | gx() & 0x3f; ++sc;}
     {int uc = vx(v | (0x3f>>sc & (int)c) << 6*sc);
       {int k = sizeof(vpl)/sizeof(vp);
       while(k--) if(uc == vpl[k].v)
         {printf("&%s;", vpl[k].nm); goto end;}}
       printf("&#x%x;", uc);}
       end: ++cc;}
  else if(!bk && c == '&') {char c = gc(4);
     int gs(char c, int r){
         int vd(char c){if('0' <= c && c <= '9') return c - '0';
            {char lc = tolower(c);
            if(r == 16 && 'a' <= lc && lc <= 'f') return lc - 'a' + 10;
            loc();
            fprintf(stderr, "Invalid digit folowing \"&#\" construct.");
            exit(0);
            return 0;}}
         int k = vd(c);
         while(1){char c = gc(4); if(c == ';') return k;
            k = r*k + vd(c);}}
     if(c == '#') {char c = gc(4);
        utf8(vx(c == 'x' || c == 'X' ? gs('0', 16) : gs(c, 10)));}
     else {int k = sizeof(vpl)/sizeof(vp);
        char st[10]; st[0] = c;
           {int n; for(n=1; n<10; ++n) {char c = gc(4);
             if(c == ';') goto e1;
             if(!isalpha(c)) break;
             st[n] = c;}
          loc(); fprintf(stderr, "%s reference\n",
            n>10?"Verbose":"Invalid");
          continue;
          e1: st[n] = 0;
  //      loc(); fprintf(stderr, "string is <%s>.\n", st);
          while(k--) if(!strcmp(st, vpl[k].nm)) {
             utf8(vpl[k].v); break;}
     if(k<0) {loc();
        fprintf(stderr, "Unrecognized reference: &%s;\n", st);}}}}
  else if(c > 127) {loc(); fprintf(stderr, "Non ASCII char.\n");}
  else putchar(c);
}
return 0;
}

