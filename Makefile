# Silence output slightly
# .SILENT:

DB := dhil_doceww

PROJECT := doceww
URL := http://localhost/dhil/doceww/public

SOLR_CORE := doceww
SOLR_URL := http://localhost:8983/solr/\#/$(SOLR_CORE)/core-overview
SOLR_HOME := /opt/homebrew/var/lib/solr/$(SOLR_CORE)

# Override any of the options above by copying them to makefile.local
-include Makefile.local

# Library of makefile targets
include etc/Makefile

## Local make file

## -- No targets yet
