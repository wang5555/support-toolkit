#!/bin/sh
# Script that copies the required dependancy files
# to the correct location inside the STK package

# List with phpBB files and directories that will be
# copied from the phpBB repository into the stk/lib/phpBB
# directory to be used by the STK. If you need a new
# phpBB file/directory included into the STK don't
# forget to add an entry into this list!
phpBB=(
	includes/auth.php
	includes/constants.php
	includes/functions.php
	includes/functions_content.php
	includes/session.php
	includes/template.php

	includes/cache/
	includes/config/
	includes/db/
	includes/request/
	includes/utf/
)

# Make sure that the script is ran from inside the
# "build" directory
basedir=$(dirname $0)
if [ $basedir != "." ]; then
	echo "This script must be ran from the STK build directory"
	exit 1
fi

# Function that handles the copy
copyLib()
{
	if [ $1 == "umil" ]; then
		echo "Copying the UMIL core"

		if [ ! -d ./stk/lib/UMIL ]; then
			mkdir -p ./stk/lib/UMIL
		fi

		cp ./vendor/UMIL/umil/root/umil/umil.php ./stk/lib/UMIL/umil.php
	else
		echo "Copying $1"
		dn=$(dirname $1)

		if [ ! -d "./stk/lib/phpBB/$dn" ]; then
			mkdir -p "./stk/lib/phpBB/$dn"
		fi

		cp -r "./vendor/phpBB/phpBB/$1" "./stk/lib/phpBB/$1"
	fi
}

# Make sure that the vendor submodules are updated
cd ..
git submodule foreach git pull

# Clear the existing lib dirs
rm -rf ./stk/lib/phpBB
rm -rf ./stk/lib/UMIL

# Copy over the phpBB files
for f in ${phpBB[@]}
do
	copyLib "$f"
done

# Copy over the UMIL core
copyLib "umil"
