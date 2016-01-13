#
# Config to deploy script
#

all: todeploy.phar

todeploy.phar:
	php build-phar.php todeploy.phar.json
	chmod +x todeploy.phar

clean:
	rm -f todeploy.phar
