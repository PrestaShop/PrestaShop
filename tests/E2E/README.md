# Ready Espresso

## Prerequisites
- Download [Selenium Standalone](http://www.seleniumhq.org/download/)
- Download [chromedriver (current v2.32)](https://chromedriver.storage.googleapis.com/index.html?path=2.32/)

## How to run the tests

### Package install

In a terminal
```
➜  npm install
```

### Launch chromedriver

In a terminal
```
➜  ./chromedriver
```

Expected
>Starting ChromeDriver 2.32.498537 (cb2f855cbc7b82e20387eaf9a43f6b99b6105061) on port 9515
>Only local connections are allowed.

### Launch selenium-standalone

In a terminal
```
➜  java -jar selenium-server-standalone-3.5.3.jar
```

Expected

```
11:26:16.687 INFO - Selenium build info: version: '3.5.3', revision: 'a88d25fe6b'
11:26:16.688 INFO - Launching a standalone Selenium Server
2017-09-27 11:26:16.714:INFO::main: Logging initialized @263ms to org.seleniumhq.jetty9.util.log.StdErrLog
11:26:16.781 INFO - Driver class not found: com.opera.core.systems.OperaDriver
11:26:16.808 INFO - Driver provider class org.openqa.selenium.ie.InternetExplorerDriver registration is skipped:
 registration capabilities Capabilities [{ensureCleanSession=true, browserName=internet explorer, version=, platform=WINDOWS}] does not match the current platform MAC
11:26:16.808 INFO - Driver provider class org.openqa.selenium.edge.EdgeDriver registration is skipped:
 registration capabilities Capabilities [{browserName=MicrosoftEdge, version=, platform=WINDOWS}] does not match the current platform MAC
11:26:16.833 INFO - Using the passthrough mode handler
2017-09-27 11:26:16.858:INFO:osjs.Server:main: jetty-9.4.5.v20170502
2017-09-27 11:26:16.886:WARN:osjs.SecurityHandler:main: ServletContext@o.s.j.s.ServletContextHandler@3e9b1010{/,null,STARTING} has uncovered http methods for path: /
2017-09-27 11:26:16.891:INFO:osjsh.ContextHandler:main: Started o.s.j.s.ServletContextHandler@3e9b1010{/,null,AVAILABLE}
2017-09-27 11:26:16.923:INFO:osjs.AbstractConnector:main: Started ServerConnector@648ba6de{HTTP/1.1,[http/1.1]}{0.0.0.0:4444}
2017-09-27 11:26:16.924:INFO:osjs.Server:main: Started @474ms
11:26:16.924 INFO - Selenium Server is up and running
```

### Launch test suite


```
➜  mocha test/campaigns/high/* --URL=URL_SITE
```

## Files and Repo

### Files

* common.webdriverio > common functions
* globals.webdriverio > classic UI map (PS core) 


### Repo

* [datas] > pictures and files to use for tests
* [campaigns] > High test suite
