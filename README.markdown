# PrestaShop 1.5.x

PrestaShop is an open-source e-commerce solution

## Contributing

To contribute to PrestaShop, you can make pull requests on the **development** branch

Make sure to respect our [coding standards](http://doc.prestashop.com/display/PS15/Coding+Standard)

If you fix an issue already present in our [bugtracker](http://forge.prestashop.com/), 
please specify the issue number in your pull request message or in the name of the branch, 
for example _PSCFV-007_

Read the [Fork a repo](https://help.github.com/articles/fork-a-repo) article 
if you aren't familiar with GitHub. 

### Quick start

Find below a summary of commands you may use in your workflow

Fork the repo on your GitHub account, and clone it on your development machine. 

<pre>
$ git clone git@github.com:username/PrestaShop.git
$ git remote
origin
</pre>

Checkout the `development` branch of your fork, create a `topic` branch from there, 
make changes, and push them to your remote. 

<pre>
$ git checkout --track origin/development
$ git branch
* development
master
$ git branch topic
$ git checkout topic
$ git branch
development
master
* topic
...
$ git commit -m
$ git push origin topic
</pre>

Add the `upstream` remote to keep track of the blessed repo. 

<pre>
$ git remote add upstream git@github.com:PrestaShop/PrestaShop.git
$ git remote
origin
upstream
</pre>

Fetch changes made by PrestaShop developers or other contributors 
on the `development` branch of the blessed repo, and push them back to your fork.

<pre>
$ git checkout development
$ git fetch upstream
$ git merge upstream/development 
$ git push origin development
</pre>