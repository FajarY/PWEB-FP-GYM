#!/bin/bash

LOCAL_DIR="./"
REMOTE_USER="azureuser"
REMOTE_HOST="20.2.139.88"
REMOTE_DIR="/home/azureuser/Websites/gymjournal.com"
SSH_KEY="~/.ssh/CryothinkServerB1s1_key.pem"

rsync -avzP -e "ssh -i $SSH_KEY" --include-from=include-sync-files-remote.txt \
    $LOCAL_DIR $REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR