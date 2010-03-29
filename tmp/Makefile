DATE=`date "+%Y%m%d"`

all:
	cvs -d /var/lib/cvs checkout cacert
	tar cjvf ../tarballs/cacert-${DATE}.tar.bz2 --exclude CVS cacert
	rm ../tarballs/current.tar.bz2
	ln -s cacert-${DATE}.tar.bz2 ../tarballs/current.tar.bz2
	tar cvjf ../tarballs/cacert-cvs.tar.bz2 /var/lib/cvs/cacert
	mv ../tarballs/cacert-cvs.tar.bz2 ../www/cacert-cvs.tar.bz2

