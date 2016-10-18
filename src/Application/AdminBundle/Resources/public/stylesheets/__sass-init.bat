@ECHO OFF
SET RUBY_BIN=C:\Ruby\bin

:: Add RUBY_BIN to the PATH
:: RUBY_BIN takes higher priority to avoid other tools conflict (mainly the DevKit)
SET PATH=%RUBY_BIN%;%PATH%
SET RUBY_BIN=

:: Display Ruby version
ruby.exe -v

cd..
compass watch --sass-dir stylesheets --css-dir css --sourcemap --output-style compressed