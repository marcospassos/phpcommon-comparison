#!/usr/bin/env bash

set -e

if [ -z ${TRAVIS_TAG} ]; then
    echo "\$TRAVIS_TAG is unset"
    exit 1
fi

if [ -z ${GH_TOKEN} ]; then
    echo "\GH_TOKEN is unset"
    exit 1
fi

if [ -z ${TRAVIS_REPO_SLUG} ]; then
    echo "\TRAVIS_REPO_SLUG is unset"
    exit 1
fi

# Build parameters
PROJECT=$(mktemp -d)
REPOSITORY=${REPOSITORY:-"https://${GH_TOKEN}@github.com/${TRAVIS_REPO_SLUG}.git"}
BRANCH=${BRANCH:-"gh-pages"}
VERSION=${TRAVIS_TAG}
VERSION_MAJOR="$(echo "${VERSION}" | cut -d '.' -f 1)"
VERSION_MINOR="$(echo "${VERSION}" | cut -d '.' -f 2)"

# Commit settings
COMMIT_AUTHOR_NAME="Travis"
COMMIT_AUTHOR_EMAIL="travis@travis-ci.org"
COMMIT_MESSAGE="Update documentation to version ${VERSION}"

# Documentation paths
MAKEFILE="Makefile"
TESTDOX_DOCS="docs/test"
TESTDOX_LATEST="${TESTDOX_DOCS}/latest.html"
TESTDOX_FILE="${VERSION}.html"
TESTDOX_VERSION="${TESTDOX_DOCS}/${TESTDOX_FILE}"
API_DOCS="docs/api"
API_LATEST="${API_DOCS}/latest"
API_DIR="${VERSION_MAJOR}.${VERSION_MINOR}"
API_VERSION="${API_DOCS}/${API_DIR}"

# Import repository
echo "Cloning repository..."
git clone "${REPOSITORY}" "${PROJECT}" --branch "${BRANCH}" --depth 1

# Change working directory
cd "${PROJECT}"

# Set identity
git config user.name "${COMMIT_AUTHOR_NAME}"
git config user.email "${COMMIT_AUTHOR_EMAIL}"

# Build the documentation
echo "Updating testdox documentation to latest version ${VERSION}..."
make -f "${MAKEFILE}" test
# Rename the file to match the version number
mv "${TESTDOX_LATEST}" "${TESTDOX_VERSION}"
# Create a latest link which points to the last version
ln -sf "${TESTDOX_FILE}" "${TESTDOX_LATEST}"

# Ensure API documentation generation for path versions
if [ ! -d ${API_VERSION} ]; then
    # Build the documentation
    echo "Updating API documentation to latest version ${VERSION}..."
    make -f "${MAKEFILE}" api
    # Rename the directory to match the version number
    mv "${API_LATEST}" "${API_VERSION}"
    # Create a latest link which points to the last version
    ln -sf "${API_DIR}" "${API_LATEST}"
fi

# Record changes to the repository
git add .
git commit -m "${COMMIT_MESSAGE}"

# Push changes to remote server
git push origin HEAD

# Delete temporary files
rm -rf "${PROJECT}"

echo "Documentation successfully updated to version ${VERSION}"