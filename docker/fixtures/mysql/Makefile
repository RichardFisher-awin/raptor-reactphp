ifndef server
  ifdef DH
    server = $(DH)
    $(info Detected '$(DH)' from Docker Environment Switcher. Go you!)
  endif
endif

server ?= docker
version ?= latest
name = react-mysql

all: build

build:
	docker build -t $(name) -f Dockerfile .

run:
	docker run --rm -it $(name) bash

start: stop
	docker run -d --name $(name) -p 3306:3306 $(name)

stop:
	@docker rm -vf $(name) ||:

exec:
	docker exec -it $(name) bash

.PHONY: all build run start stop exec
