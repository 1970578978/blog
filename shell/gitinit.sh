cd $1
git fetch >> /dev/null
git merge origin/master
exit 0
