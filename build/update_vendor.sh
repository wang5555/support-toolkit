#!/bin/sh
# Script that copies the required dependancy files
# to the correct location inside the STK package

# List with phpBB files, if you need a new phpBB file
# to be used in the STK make sure that you add it to
# this list!
files=(

)

# List with phpBB directories, if you need a complete
# directory you can add them to this list instead of
# having to add all the files seperate
dirs=(

)

# Make sure that the vendor submodules are updated
cd ..
git submodule foreach git pull

# Make sure that the destinations exist
if [ ! -d ./stk/lib/phpBB ]; then
    mkdir -p ./stk/lib/phpBB
fi
if [ ! -d ./stk/lib/UMIL ]; then
	mkdir -p ./stk/lib/UMIL
fi

# Copy over the phpBB files
for f in ${files[@]}
do
	echo "Copying $f"
	dn=`dirname $f`

	if [ ! -d "./stk/lib/phpBB/$dn" ]; then
		mkdir -p "./stk/lib/phpBB/$dn"
	fi

	cp "./vendor/phpBB/phpBB/$f" "./stk/lib/phpBB/$f"
done

# Copy over the phpBB directories
for d in ${dirs[@]}
do
	echo "Copying $d"
	dn=`dirname $d`

	if [ ! -d "./stk/lib/phpBB/$dn" ]; then
		mkdir -p "./stk/lib/stk/$dn"
	fi

	cp -r "./vendor/phpBB/phpBB/$d" "./stk/lib/phpBB/$d"
done

# Copy over the UMIL core
echo "Copying the UMIL core"
cp ./vendor/UMIL/umil/root/umil/umil.php ./stk/lib/UMIL/umil.php