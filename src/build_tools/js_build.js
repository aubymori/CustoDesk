const gulp                   = require("gulp");
const GulpPreprocess         = require("gulp-preprocess");
const GulpTerser             = require("gulp-terser");
const path                   = require("path");
const vfl                    = require("./vfl");

function build()
{
    let stream = gulp.src("../js/[^_]*.js")
                    .pipe(GulpPreprocess({
                        includeBase: path.resolve(path.dirname(__filename), "../js/")
                    }))
                    .pipe(GulpTerser({
                        ecma: 3,
                        compress: true,
                        mangle: true,
                    }))
                    .pipe(vfl.gulp("s/jsbin"))
                    .pipe(gulp.dest("../../s/jsbin/"));
    stream.on("end", vfl.writeMappings);
    return stream;
}

module.exports = build;