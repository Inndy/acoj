/*
 * ACOJ Judge Client
 * ./function.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
// convert newline to unix representation
void cvtnlunix(char*s){
	char*ns=s;
	while(*s){
		if(*s!='\r')
			*ns++=*s;
		s++;
	}
	if(ns!=s&&ns[-1]!='\n')
		*ns++='\n';
	*ns++=0;
}
#include<cstring>
void remove_endl(char*s){
	int l=strlen(s);
	if(l!=0&&s[l-1]=='\n')
		s[l-1]=0;
}
