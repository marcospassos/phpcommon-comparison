# Resource paths
space :=
space +=
BASE_DIR := $(subst $(space),\ ,$(abspath $(dir $(lastword $(MAKEFILE_LIST)))))
DOC_VENDOR := $(BASE_DIR)/vendor
APIGEN_CONFIG := $(BASE_DIR)/apigen.neon
API_DOC := $(BASE_DIR)/docs/api/latest
TEST_DOC := $(BASE_DIR)/docs/test/latest.html
PROJECT := $(DOC_VENDOR)/phpcommon/comparison
PROJECT_SOURCE := $(PROJECT)/src
PROJECT_VENDOR := $(PROJECT)/vendor

# Executable paths
APIGEN := $(BASE_DIR)/bin/apigen
PHPUNIT := $(BASE_DIR)/bin/phpunit

# Generate complete documentation
all: api test clean

# Install documentation dependencies
$(DOC_VENDOR):
	composer install -d $(BASE_DIR)

$(PHPUNIT): $(DOC_VENDOR)
$(APIGEN): $(DOC_VENDOR)

# Install dependencies
$(PROJECT_VENDOR): $(DOC_VENDOR)
	composer install -d $(PROJECT)

# Generate API documentation
api: $(PROJECT_VENDOR) $(APIGEN)
	rm -rf $(API_DOC)
	$(APIGEN) generate --config $(APIGEN_CONFIG) -s $(PROJECT_SOURCE) -d $(API_DOC)

# Generate test documentation
test: $(PROJECT_VENDOR) $(PHPUNIT)
	rm -f $(TEST_DOC)
	$(PHPUNIT) --testdox-html $(TEST_DOC) -c $(PROJECT)

# Delete temporary files
clean:
	rm -rf $(PROJECT)

.PHONY: all api test clean