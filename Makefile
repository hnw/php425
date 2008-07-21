VERSION=0.0.1
dist_target=php425-$(VERSION)

all:
	$(MAKE) -C libs

clean:
	$(MAKE) -C libs clean
	-rm -rf $(dist_target) $(dist_target).tar.bz2

dist:
	-mkdir $(dist_target)
	cp -r libs $(dist_target)
	find $(dist_target) -type f ! -name '*.php'|xargs rm -rf
	cp -r vendor $(dist_target)
	find $(dist_target) -name '.svn'|xargs rm -rf
	tar jcvf $(dist_target).tar.bz2 $(dist_target)
