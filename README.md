# local-ssl for Mac.
Creates a Local Certificate authority, generates local certs, and wires it up to *Docker Compose*.

**You might to have to regenerate your SSL Certs if you want this to work on Mac 10.15** ***Sorry, but Apple did it.***

## Getting Started
*Please don't run this on a PC. I have no idea what'll happen.*

* run `npx degit WraithKenny/dev`
* do some light search and replace on a few strings like `some-theme` (rename folder too), `wordpress.org.test`, and `Some_Theme` (See below).
* then `npm i && npm run dev`

The last command will do a lot of things.
 1. Set a domain in your `hosts` file (default `www.wordpress.org.test` but feel free to search/replace in in **all** of the files before running it.)
 1. Install a local **Certificate Authority** if you don't have one. (Will ask you to set a password) This is saved in your user root `~` for future usage.
 1. Will attempt to Trust the CA (will prompt for user password)
 1. Create a local Cert (Will prompt you for your local-CA password)
 1. Install npm packages like normal
 1. Will start up, and bring down *Docker*, to set up your Volumes.
 1. Will install composer files (Coding Standard & themes/plugins)
 1. Will restart *Docker*
 1. Will start *Webpack* and *BrowserSync* in dev mode, opening your browser to your SSL dev environment with Hot Reloading.

## The URL variable and the local domain
There's an `npm` script for adding your local domain to your `hosts` file. Change `URL` to match what's in `./ssl/localhost.ext`

This `URL` variable is used (and should be changed) in 3 locations
 1. `package.json` (in the `"add-localhost"` script)
 1. `gulpfile.babel.js` (`host` and `proxy` fields for *BrowserSync*)
 1. `localhost.ext` (the file that is used to generate the *SSL Cert*)

## The *Docker Compose* file
The `docker-compose.yml` file is pretty generic, except its set up for SSL (by mounting some config files and your certs), and it uses environmental vars.

Feel free to change the `.env` file vars. `COMPOSE_PROJECT_NAME` is so Composer doesn't get confused between projects that are in folders with the same name. Also rename your theme folder which is named `some-theme` in this repo with whatever you change `THEME_FOLDER` to. Keep in mind, I've used this string as both the folder name of the theme, and the localization string in the theme.

## Extras
This project is set up for SSL on local dev, hot reloading, SASS Compiling, Babel/Webpack bundling, VSCode boiler, Coding Standards, etc.

It also comes with scripts to persist your database to file, so you can commit it to `git` if you'd like. When Docker boots up, and it's Volumes (like for the database), it'll automatically load that saved database.
