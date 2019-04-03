workflow "Code Quality" {
  on = "push"
  resolves = [
    "PHP-CS-Fixer",
    "PHP 5.6 Syntax check",
  ]
}

action "PHP 5.6 Syntax check" {
  uses = "docker://prestashop/github-action-php-lint:5.6"
  args = "-name \"*.php\" ! -path \"./vendor/*\" ! -path \"./tools/*\" ! -path \"./modules/*\""
}

action "PHP 7.2 Syntax check" {
  uses = "docker://prestashop/github-action-php-lint:7.2"
  args = "-name \"*.php\" ! -path \"./vendor/*\" ! -path \"./tools/*\" ! -path \"./modules/*\""
}

action "PHP-CS-Fixer" {
  needs = ["PHP 7.2 Syntax check"]
  uses = "docker://oskarstark/php-cs-fixer-ga"
  args = "--config=.php_cs.dist --diff --diff-format=udiff --dry-run"
}
