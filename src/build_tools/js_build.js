const gulp                   = require("gulp");
const GulpPreprocess         = require("gulp-preprocess");
const GulpBabel              = require("gulp-babel");
const path                   = require("path");
const vfl                    = require("./vfl");

function build()
{
    let stream = gulp.src("../js/[^_]*.js")
                    .pipe(GulpPreprocess({
                        includeBase: path.resolve(path.dirname(__filename), "../js/")
                    }))
                    .pipe(GulpBabel({
                        presets: [
                            "@babel/preset-env"
                        ],
                        targets: "firefox 3",
                        minified: true,
                    }))
                    .pipe(vfl.gulp("s/jsbin"))
                    .pipe(gulp.dest("../../s/jsbin/"));
    stream.on("end", vfl.writeMappings);
    return stream;
}

module.exports = build;