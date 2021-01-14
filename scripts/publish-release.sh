#!/bin/bash

# Check state.
if [ -z "${NEW_GIT_VERSION_WITHOUT_V_PREFIX}" ]; then
    echo "No \$NEW_GIT_VERSION_WITHOUT_V_PREFIX env var found. Exiting."
    exit 1
fi

# Initilize
platform='unknown'
unamestr=`uname`
if [[ "$unamestr" == 'Linux' ]]; then
   platform='linux'
elif [[ "$unamestr" == 'FreeBSD' ]]; then
   platform='freebsd'
elif [[ "$unamestr" == 'Darwin' ]]; then
   platform='osx'
fi
git_base_dir=`git rev-parse --show-toplevel`

# Update version everywhere (add and commit changes), tag and release
git checkout main
if [[ $platform == 'linux' ]]; then
   sed -i -E "s/Version: [0-9]+\.[0-9]+\.[0-9]/Version: $NEW_GIT_VERSION_WITHOUT_V_PREFIX/" $git_base_dir/crowdsec.php
   sed -i -E "s/Stable tag: [0-9]+\.[0-9]+\.[0-9]/Stable tag: $NEW_GIT_VERSION_WITHOUT_V_PREFIX/" $git_base_dir/crowdsec.php
   sed -i -E "s/v[0-9]+\.[0-9]+\.[0-9]/v$NEW_GIT_VERSION_WITHOUT_V_PREFIX/" $git_base_dir/inc/constants.php
else
   sed -i "" -E "s/Version: [0-9]+\.[0-9]+\.[0-9]/Version: $NEW_GIT_VERSION_WITHOUT_V_PREFIX/" $git_base_dir/crowdsec.php
   sed -i "" -E "s/Stable tag: [0-9]+\.[0-9]+\.[0-9]/Stable tag: $NEW_GIT_VERSION_WITHOUT_V_PREFIX/" $git_base_dir/crowdsec.php
   sed -i "" -E "s/v[0-9]+\.[0-9]+\.[0-9]/v$NEW_GIT_VERSION_WITHOUT_V_PREFIX/" $git_base_dir/inc/constants.php
fi
git add $git_base_dir/inc/constants.php
git add $git_base_dir/crowdsec.php

git commit -m "bump version to v$NEW_GIT_VERSION_WITHOUT_V_PREFIX"
git tag v$NEW_GIT_VERSION_WITHOUT_V_PREFIX
git push
git push origin v$NEW_GIT_VERSION_WITHOUT_V_PREFIX
gh release create --draft v$NEW_GIT_VERSION_WITHOUT_V_PREFIX --title v$NEW_GIT_VERSION_WITHOUT_V_PREFIX


P1 - "Force Captcha wall for these decision types:"

P2 - add the trusted proxy ip from cloudflare by default https://www.cloudflare.com/fr-fr/ips/
P2 - Afficher les logs
P2 Stream mode passif : permettre de ne pas raffraichir la liste (Ex avec un REDIS partagé)

P3 - "Force Ban wall for these decision types:"
P3 - "Bypass any walls for these decision types:"