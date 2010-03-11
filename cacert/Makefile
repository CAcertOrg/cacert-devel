all:
	xgettext -s -o messages.po --no-wrap --foreign-user includes/*.php www/*.php pages/account/*.php pages/index/*.php pages/wot/*.php pages/gpg/*.php pages/disputes/*.php pages/help/*.php pages/disputes/*.php scripts/removedead.php
	perl cacertupload.pl
	cd locale; php make.php

other: all
	cat messages.po|sed "s/CHARSET/iso-8859-1/"|sed "s/PACKAGE VERSION/CAcert/"|sed "s/This file is put in the public domain./This file is distributed under the same license as the CAcert package./"|sed "s/# SOME DESCRIPTIVE TITLE.//" > messages.po
