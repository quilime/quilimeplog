all:
	mkdir -p templates/cache;
	chmod a+rwX templates/cache;
	mkdir -p tmp;
	chmod a+rwX tmp;
	mkdir -p www/media;
	chmod a+rwX www/media;	
	mkdir -p www/include;
	chmod a+rwX www/include;	
	mkdir -p www/thumbs;
	chmod a+rwX www/thumbs;	
	cd lib && make all;

clean:
	rm -rf templates/cache;
	rm -rf tmp;
	cd lib && make clean;
