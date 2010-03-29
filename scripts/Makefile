all: runserver.c runclient.c
	gcc -O2 -o runserver runserver.c
	gcc -O2 -o runclient runclient.c
	gcc -O2 -o rungpg rungpg.c
	chown root:chrapach runserver runclient rungpg
	chmod 4710 runserver runclient rungpg

clean:
	rm -f runserver runclient rungpg test

test: test.c
	gcc -O2 -o test test.c
	chown root:chrapach test
	chmod 4710 test
