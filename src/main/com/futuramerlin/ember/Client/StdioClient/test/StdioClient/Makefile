NAME="StdioClient"
PKG="Client"

all:
	$(info $(shell echo "Trying to build $(NAME)..."))
	$(shell javac -cp . ./$(NAME).java)
	$(shell jar cfe $(NAME).jar com.futuramerlin.ember.$(PKG).$(NAME) .)
	$(info $(shell echo "Your $(NAME) is now ready in $(NAME).jar."))

clean:
	$(shell cd src; find . -type f -name "*.class" -delete)