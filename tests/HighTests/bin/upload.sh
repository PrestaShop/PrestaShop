#!/bin/sh

FIXED_BRANCH=$(echo $TRAVIS_BRANCH | sed 's/\//-/g')
ARCHIVE=$FIXED_BRANCH-$(date +%Y-%m-%d_%H_%M_%S)-$TRAVIS_COMMIT.tar.bz2
DIR="test/itg/$PS_VERSION/screenshots"

# look for empty dir
if [ "$(ls -A $DIR)" ]; then
    echo "Creating archive $ARCHIVE"
    tar -cjf $ARCHIVE -C $DIR .
    FILESIZE=$(stat -c%s "$ARCHIVE")
    echo "Finished archive (size $FILESIZE), starting Google Drive upload"
    ./bin/gdrive-linux-x64 upload --refresh-token $GDRIVE_REFRESH_TOKEN --parent $GDRIVE_DIR "$ARCHIVE"
    echo "Finished Google Drive upload"
else
    echo "$DIR is empty"
fi