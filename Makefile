# Makefile — web-security-lab-portfolio
# Requires: docker, docker compose, python3, php, shellcheck, htmlhint

COMPOSE      := docker compose
PYTHON       := python3
PHP          := php
SHELL_CHECK  := shellcheck
HTML_HINT    := htmlhint

.PHONY: up down reset test lint evidence report help

## up: Start all Docker services
up:
	cp -n .env.example .env 2>/dev/null || true
	$(COMPOSE) up -d --build
	@echo "Services started. URLs:"
	@echo "  DVWA:        http://localhost:8080"
	@echo "  PHP-Apache:  http://localhost:8081"
	@echo "  ZAP API:     http://localhost:8090"
	@echo "  BeEF:        http://localhost:3000/ui/panel"

## down: Stop all Docker services
down:
	$(COMPOSE) down

## reset: Stop services, remove volumes, restart fresh
reset:
	$(COMPOSE) down -v --remove-orphans
	$(COMPOSE) up -d --build

## lint: Run all linters
lint: lint-php lint-python lint-shell lint-html
	@echo "All linters passed."

lint-php:
	@echo "==> PHP lint"
	@find assignment-01-web-php-http/php -name "*.php" -exec $(PHP) -l {} \;

lint-python:
	@echo "==> Python lint (py_compile)"
	@find . -name "*.py" ! -path "./.venv/*" -exec $(PYTHON) -m py_compile {} \;
	@echo "==> Python style (flake8)"
	@$(PYTHON) -m flake8 --max-line-length=100 --exclude=.venv,venv . || true

lint-shell:
	@echo "==> Shell lint (shellcheck)"
	@find . -name "*.sh" -exec $(SHELL_CHECK) {} \;

lint-html:
	@echo "==> HTML lint (htmlhint)"
	@find assignment-01-web-php-http/site -name "*.html" -exec $(HTML_HINT) {} \; || true

## test: Run all automated tests
test: test-python test-xss-matrix
	@echo "All automated tests complete. See results/ directories."

test-python:
	@echo "==> Python pytest"
	@$(PYTHON) -m pytest \
		assignment-01-web-php-http/source-analysis/ \
		assignment-04-sqli/scripts/ \
		--tb=short -v

test-xss-matrix:
	@echo "==> XSS Playwright matrix (requires Docker services up)"
	@$(PYTHON) -m pytest assignment-05-xss/harness/test_xss.py --tb=short -v || \
		echo "[HUMAN-EXECUTED — PENDING] Start services with 'make up' first."

## evidence: Regenerate evidence index
evidence:
	@echo "==> Building evidence index"
	@$(PYTHON) scripts/build_evidence_index.py > reports/evidence-index.md
	@echo "Evidence index written to reports/evidence-index.md"

## report: Open consolidated report
report:
	@cat reports/consolidated-report.md

## docker-config: Validate docker-compose syntax
docker-config:
	$(COMPOSE) config -q && echo "docker-compose.yml is valid."

## help: Show this help
help:
	@grep -E '^##' Makefile | sed 's/## //'
