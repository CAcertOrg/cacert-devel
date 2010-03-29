#include<stdio.h>

int main(int argc, char *argv[])
{
	char *args[1];

	args[0] = NULL;

	setuid(0);
	setgid(0);

	execv("/www/scripts/servercerts.php", args);
}
