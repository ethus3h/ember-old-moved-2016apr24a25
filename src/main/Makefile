define compilemodule =
	$(info $(shell echo "Trying to build $(firstword $^)..."))
	$(shell javac -cp ../../lib ./$(firstword $^).java)
	$(shell jar cfe $(firstword $^).jar $(firstword $^) .)
	$(info $(shell echo "Your $(firstword $^) is now ready in $(firstword $^).jar."))
endef

all:
	$(shell ./build.sh)

stdioclient : StdioClient
	$(compilemodule)

.PHONY: clean
clean:
	$(shell cd src; find . -type f -name "*.class" -delete)