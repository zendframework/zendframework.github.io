# Makefile for ZF documentation landing page
#
# General usage:
#
#     make all
#
# Once done, run:
#
#     git add -A .
#     git commit
#     git push origin
#
# After running the `make all` command, you might want to preview the
# results before committing:
#
#     php -S 0:8000 -t .

.PHONY : all ready clean mkdocs build

all: build

zf-mkdoc-theme:
	- git clone git://github.com/zendframework/zf-mkdoc-theme.git

ready:
	- rm -Rf css img index.html 404.html js search sitemap.xml docs/html
	- mkdir -p docs/html

zf-component-list.json:
	- wget https://zendframework.github.io/zf-mkdoc-theme/scripts/zf-component-list.json

docs/book/index.html:
	- php docs/scripts/prepare_component_list.php

mkdocs: zf-mkdoc-theme zf-component-list.json docs/book/index.html
	- ./zf-mkdoc-theme/build.sh -u https://zendframework.github.io
	- cp -a docs/html/* .
	- mv 404/index.html 404.html
	- sed --in-place -r -e 's/\"\.\./"/g' 404.html
	- sed --in-place -r -e 's/href\=\"\"/href="\/"/g' 404.html
	- sed --in-place -r -e 's/<a [^>]+>Not Found<\/a>//g' 404.html
	- sed --in-place -r -e 's/<a [^>]+>404\/?<\/a>//g' index.html
	- rm index.html.dist

build: ready mkdocs clean
	@echo "[DONE]"
	@echo ""
	@echo "Now run:"
	@echo ""
	@echo "    git add -A ."
	@echo "    git commit"
	@echo "    git push origin"
	@echo ""
	@echo "After running the 'make all' command, you might want to preview the"
	@echo "results before committing, using 'php -S 0:8000 -t .'"

clean:
	- rm -Rf zf-mkdoc-theme
	- rm zf-component-list.json
	- rm docs/book/index.html
	- rm -Rf 404
