YACC_FILES = $(wildcard *.y)
PHP_YACC_FILES = $(YACC_FILES:.y=.phpy)
PHP_FILES = $(PHP_YACC_FILES:.phpy=.php)

KMYACC_OPTIONS = $(if $(DEBUG), -t)

all: $(PHP_FILES)

$(PHP_FILES): $(PHP_YACC_FILES)

clean:
	rm -f zend_language_parser-*.*.*.php zend_language_parser-*.*.*.phpy

.SUFFIXES: .y .phpy
.y.phpy:
	./y2phpy.rb $< > $@

.SUFFIXES: .phpy .php
.phpy.php:
	kmyacc $(KMYACC_OPTIONS) $<
