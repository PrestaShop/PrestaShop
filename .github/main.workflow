workflow "Code Quality" {
  on = "push"
  resolves = [
    "PHP 7.2 Syntax check",
  ]
}

action "PHP 7.2 Syntax check" {
  uses = "docker://prestashop/github-action-php-lint:7.2"
  args = "! -path \"./vendor/*\" ! -path \"./tools/*\" ! -path \"./modules/*\""
}
