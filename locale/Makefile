#
# This Makefile will download the translations from our translation server (if
# they don't exist yet) and compile them. Try target help for more information
#

################################################################################
###                                 Download                                 ###
################################################################################

DOWNLOAD_SERVER := translations.cacert.org
PO_URL_TEMPLATE := http://$(DOWNLOAD_SERVER)/export/cacert/%/messages.po

# Only use languages that have 10% or more of translated strings
AUTO_LANGS := \
en \
de \
nl \
pt_BR \
fr \
sv \
it \
es \
hu \
fi \
ja \
bg \
pt \
da \
pl \
zh_CN \
ru \
lv \
cs \
zh_TW \
el \
tr \
ar \

LANGS := \
ar \
bg \
cs \
da \
de \
el \
en \
es \
fa \
fi \
fr \
he \
hr \
hu \
id \
is \
it \
ja \
ka \
ko \
lv \
nb \
nl \
pl \
pt \
pt_BR \
ro \
ru \
sl \
sv \
th \
tr \
uk \
zh_CN \
zh_TW \


PO_FILE_TEMPLATE := %/messages.po
MO_FILE_TEMPLATE := %/LC_MESSAGES/messages.mo


# target: all - Build locales downloading po files
.PHONY: all
all: $(AUTO_LANGS)


# target: help - Display callable targets
.PHONY: help
help:
	@egrep "^# target:" [Mm]akefile


# target: clean - remove the build directories
RM := rm -rf
.PHONY: clean
clean:
	-$(RM) $(LANGS:%=%/)


# target: <lang> - build this particular language
.PHONY: $(LANGS)
$(LANGS): %: $(MO_FILE_TEMPLATE)


$(LANGS:%=$(MO_FILE_TEMPLATE)): $(MO_FILE_TEMPLATE): $(PO_FILE_TEMPLATE)
	mkdir -p $(@D)
#filter obsolete translations
	grep --invert-match '^#~ ' $< | \
		msgfmt --check --output-file $@ -


.PHONY: $(LANGS:%=$(PO_FILE_TEMPLATE))
$(LANGS:%=$(PO_FILE_TEMPLATE)):
	mkdir -p $(@D)
	wget --output-document - '$(@:$(PO_FILE_TEMPLATE)=$(PO_URL_TEMPLATE))' | \
		php -f escape_special_chars.php \
		> $@




################################################################################
###                                 Upload                                   ###
################################################################################

UPLOAD_SERVER := $(DOWNLOAD_SERVER)
SSH_USER := critical
SSH_OPTIONS :=
SCP_OPTIONS := $(SSH_OPTIONS)

FILE_OWNER := www-data

POT_UPLOAD_PATH := /var/www/Pootle/po/cacert/templates/messages.pot
MANAGE_PY := /var/www/Pootle/manage.py

VERSION := Production
DESCRITPION := LibreSSL - CAcert web application (localisation files)
COPYRIGHT_YEAR := 2004-$(shell date +\%Y)
PACKAGE := LibreSSL

GETTEXT_FILE_PATTERN := \
../CommModule/client.pl \
../includes/*.php \
../includes/*/*.php \
../pages/*/*.php \
../scripts/*.php \
../www/*.php \
../www/[a-z]*/*.php \
# ../tverify/*.php \
# ../tverify/*/*.php \

GETTEXT_FILES := $(wildcard $(GETTEXT_FILE_PATTERN))

# target: template - create the gettext template file, if you want to upload it
# target:            onto the translation server you can directly use the
# target:            target "upload"
.PHONY: template
template: messages.pot

# target: template.clean - remove anything that was created during the build of
# target:                  the template file
.PHONY: template.clean
template.clean:
	-$(RM) messages.pot


# target: upload - upload the template to the translation server
.PHONY: upload
upload: messages.pot
	scp $(SCP_OPTIONS) messages.pot $(SSH_USER)@$(UPLOAD_SERVER):$(POT_UPLOAD_PATH)
	ssh $(SSH_OPTIONS) $(SSH_USER)@$(UPLOAD_SERVER) "sudo -u $(FILE_OWNER) pootle-update cacert"

# target: upload.clean - remove anything that was created during the upload
.PHONY: upload.clean
upload.clean: template.clean

messages.pot: $(GETTEXT_FILES)
	xgettext --output - --sort-by-file --copyright-holder "CAcert Inc." \
		--package-name "CAcert" --package-version "$(VERSION)" \
		--msgid-bugs-address "translations-admin@cacert.org" \
		--add-comments=TRANSLATORS $^ | \
		# replace place holders in the lines before the first msgid\
		sed '1,/^msgid/ { s/SOME DESCRIPTIVE TITLE/$(DESCRITPION)/; s/YEAR/$(COPYRIGHT_YEAR)/; s/PACKAGE/$(PACKAGE)/ }' \
		> $@

