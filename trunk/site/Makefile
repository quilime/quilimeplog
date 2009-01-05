all:
    Smarty
	mkdir -p tmp
	chmod a+rwX tmp
	mkdir -p templates/cache
	chmod a+rwX templates/cache
	mkdir -p www/media
	chmod a+rwX www/media
	mkdir -p www/include
	chmod a+rwX www/include
	mkdir -p www/thumbs
	chmod a+rwX www/thumbs
	cd lib && make all    
    
Smarty:
    curl -sOL http://www.smarty.net/distributions/Smarty-2.6.18.tar.gz
    tar -xzf Smarty-2.6.18.tar.gz
    mv Smarty-2.6.18/libs lib/Smarty
    rm -rf Smarty-2.6.18 Smarty-2.6.18.tar.gz

clean:
    rm -rf Smarty
	rm -rf templates/cache
	rm -rf tmp
	cd lib && make clean
