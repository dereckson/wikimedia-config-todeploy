#
# Config to deploy script
#

all: todeploy.phar

todeploy.phar:
	php build-phar.php todeploy.phar.json
	
clean:
	rm -f todeploy.phar
