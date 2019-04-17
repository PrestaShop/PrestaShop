workflow "Code Quality" {
  on = "push"
  resolves = [
    "PHP 5.6 Syntax check",
    "PHP 7.2 Syntax check",
  ]
}

action "PHP 5.6 Syntax check" {
  uses = "docker://prestashop/github-action-php-lint:5.6"
  args = "! -path \"./vendor/*\" ! -path \"./tools/*\" ! -path \"./modules/*\""
}

action "PHP 7.2 Syntax check" {
  uses = "docker://prestashop/github-action-php-lint:7.2"
  args = "! -path \"./vendor/*\" ! -path \"./tools/*\" ! -path \"./modules/*\""
}
