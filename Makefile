# Docker host Detection from http://go/docker-switcher
ifndef server
  ifdef DH
    server = $(DH)
    $(info Detected '$(DH)' from Docker Environment Switcher. Go you!)
  endif
endif
ifndef PORT_HTTP
    PORT_HTTP = 80
endif

server ?= docker
version ?= latest
port_mapping ?= -p $(PORT_HTTP):80
targetEnvironment ?= dev
jenkinsUser ?= unknownUser
jenkinsApiKey ?= unknownApiKey
dockerImage ?= LATEST

all: build

build: build-mysql build-app

build-mysql:
	cd docker/fixtures/mysql && $(MAKE) build version=$(version)

build-app:
	tar --exclude-from=.dockerignore -czf - . | docker build -t react -f docker/Dockerfile -

run:
	docker run --rm -it react bash

exec:
	docker exec -it react bash

start: stop
	# MySQL
	cd docker/fixtures/mysql && $(MAKE) start

	# Datafeeds
	docker run -d -e ZANOX_SERVER_NAME=react.$(server) --name react \
	    $(port_mapping) \
		--link react-mysql:mysql.local \
		react
stop:
	cd docker/fixtures/mysql && $(MAKE) stop
	@docker rm -vf react ||:

rsync:
ifneq ($(wildcard vendor ),)
	$(info Vendor exists, including it)
	$(eval RSYNC_VENDOR := --include=vendor --filter="+ vendor")
endif
	@printf "react" | xargs -n1 -P1 -ICONTAINER rsync \
		-e "docker exec -i" --blocking-io -avz --delete \
		--no-perms --no-owner --no-group \
		$(RSYNC_VENDOR) \
		--exclude-from=".dockerignore" \
		--exclude-from=".gitignore" \
		--checksum \
		--no-times \
		--itemize-changes \
		. CONTAINER:/react/

# Phoney target list
# All targets have to be listed here
# This is Makefile specific: A Phony target is one that does't create a file
# with the name of the target. All our targets are likely always phoney.
# ALWAYS UPDATE THIS LIST: The behaviour is really hard to debug if you don't
.PHONY: all build build-mysql build-app run exec start stop rsync
