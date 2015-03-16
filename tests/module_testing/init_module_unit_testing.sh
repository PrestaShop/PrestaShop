#!/usr/bin/env sh

function help
{
	echo "
	Usage: init_module_unit_testing.sh -m <module_directory>

	-m <module_directory>    The module name, corresponding to the directory where you need to init testing tools
	-h                       This help message. Exits after printing...

	Init testing tools on module :
	==============================
	* This script creates automatically the structure of unit tests in the desired module directory.
	* This script is supposed to be executed while inside module_testing folder.
	"
}

declare module_name

if [ $# -eq 0 ]
  then
    echo "No arguments supplied"
    help "$@"
    exit 1
fi

while [ $# -gt 0 ]
do
	case $1 in
		-h|-help)
			help "$@"
			exit 0
			;;
		-m)
			shift
            module_name=$1
		    if [ -z "$module_name" ]; then
                echo "Module name is empty"
                help "$@"
                exit 1
            fi
			;;
		*)
			echo "Unknown argument specified: $1"
			help "$@"
			exit 1
			;;
	esac
	shift
done


modulepath=../../modules/${module_name}

# Create test folders
mkdir ${modulepath}/tests
mkdir ${modulepath}/tests/Unit
mkdir ${modulepath}/tests/Integration

# Copy settings files
cp ../phpunit.xml ${modulepath}/tests
cp ./bootstrap.php ${modulepath}/tests
cp ./phpunit ${modulepath}/tests
cp ./DummyTest.php ${modulepath}/tests/Unit

echo "You should now be able to create tests in your module $module_name :
    Just go to your module tests directory (cd $modulepath/tests) and run ./phpunit.
    We added a DummyTest.php file in order to check that everything works fine.
"
