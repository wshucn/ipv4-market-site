/*!
 Stencil CLI (CommonJS) v2.10.0 | MIT Licensed | https://stenciljs.com
 */
'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

function _interopNamespace(e) {
    if (e && e.__esModule) return e;
    var n = Object.create(null);
    if (e) {
        Object.keys(e).forEach(function (k) {
            if (k !== 'default') {
                var d = Object.getOwnPropertyDescriptor(e, k);
                Object.defineProperty(n, k, d.get ? d : {
                    enumerable: true,
                    get: function () {
                        return e[k];
                    }
                });
            }
        });
    }
    n['default'] = e;
    return Object.freeze(n);
}

const toLowerCase = (str) => str.toLowerCase();
const dashToPascalCase = (str) => toLowerCase(str)
    .split('-')
    .map((segment) => segment.charAt(0).toUpperCase() + segment.slice(1))
    .join('');
const isFunction = (v) => typeof v === 'function';
const isString = (v) => typeof v === 'string';

const buildError = (diagnostics) => {
    const diagnostic = {
        level: 'error',
        type: 'build',
        header: 'Build Error',
        messageText: 'build error',
        relFilePath: null,
        absFilePath: null,
        lines: [],
    };
    if (diagnostics) {
        diagnostics.push(diagnostic);
    }
    return diagnostic;
};
const catchError = (diagnostics, err, msg) => {
    const diagnostic = {
        level: 'error',
        type: 'build',
        header: 'Build Error',
        messageText: 'build error',
        relFilePath: null,
        absFilePath: null,
        lines: [],
    };
    if (isString(msg)) {
        diagnostic.messageText = msg;
    }
    else if (err != null) {
        if (err.stack != null) {
            diagnostic.messageText = err.stack.toString();
        }
        else {
            if (err.message != null) {
                diagnostic.messageText = err.message.toString();
            }
            else {
                diagnostic.messageText = err.toString();
            }
        }
    }
    if (diagnostics != null && !shouldIgnoreError(diagnostic.messageText)) {
        diagnostics.push(diagnostic);
    }
    return diagnostic;
};
const hasError = (diagnostics) => {
    if (diagnostics == null || diagnostics.length === 0) {
        return false;
    }
    return diagnostics.some((d) => d.level === 'error' && d.type !== 'runtime');
};
const shouldIgnoreError = (msg) => {
    return msg === TASK_CANCELED_MSG;
};
const TASK_CANCELED_MSG = `task canceled`;

/**
 * Convert Windows backslash paths to slash paths: foo\\bar ➔ foo/bar
 * Forward-slash paths can be used in Windows as long as they're not
 * extended-length paths and don't contain any non-ascii characters.
 * This was created since the path methods in Node.js outputs \\ paths on Windows.
 */
const normalizePath = (path) => {
    if (typeof path !== 'string') {
        throw new Error(`invalid path to normalize`);
    }
    path = normalizeSlashes(path.trim());
    const components = pathComponents(path, getRootLength(path));
    const reducedComponents = reducePathComponents(components);
    const rootPart = reducedComponents[0];
    const secondPart = reducedComponents[1];
    const normalized = rootPart + reducedComponents.slice(1).join('/');
    if (normalized === '') {
        return '.';
    }
    if (rootPart === '' &&
        secondPart &&
        path.includes('/') &&
        !secondPart.startsWith('.') &&
        !secondPart.startsWith('@')) {
        return './' + normalized;
    }
    return normalized;
};
const normalizeSlashes = (path) => path.replace(backslashRegExp, '/');
const altDirectorySeparator = '\\';
const urlSchemeSeparator = '://';
const backslashRegExp = /\\/g;
const reducePathComponents = (components) => {
    if (!Array.isArray(components) || components.length === 0) {
        return [];
    }
    const reduced = [components[0]];
    for (let i = 1; i < components.length; i++) {
        const component = components[i];
        if (!component)
            continue;
        if (component === '.')
            continue;
        if (component === '..') {
            if (reduced.length > 1) {
                if (reduced[reduced.length - 1] !== '..') {
                    reduced.pop();
                    continue;
                }
            }
            else if (reduced[0])
                continue;
        }
        reduced.push(component);
    }
    return reduced;
};
const getRootLength = (path) => {
    const rootLength = getEncodedRootLength(path);
    return rootLength < 0 ? ~rootLength : rootLength;
};
const getEncodedRootLength = (path) => {
    if (!path)
        return 0;
    const ch0 = path.charCodeAt(0);
    // POSIX or UNC
    if (ch0 === 47 /* slash */ || ch0 === 92 /* backslash */) {
        if (path.charCodeAt(1) !== ch0)
            return 1; // POSIX: "/" (or non-normalized "\")
        const p1 = path.indexOf(ch0 === 47 /* slash */ ? '/' : altDirectorySeparator, 2);
        if (p1 < 0)
            return path.length; // UNC: "//server" or "\\server"
        return p1 + 1; // UNC: "//server/" or "\\server\"
    }
    // DOS
    if (isVolumeCharacter(ch0) && path.charCodeAt(1) === 58 /* colon */) {
        const ch2 = path.charCodeAt(2);
        if (ch2 === 47 /* slash */ || ch2 === 92 /* backslash */)
            return 3; // DOS: "c:/" or "c:\"
        if (path.length === 2)
            return 2; // DOS: "c:" (but not "c:d")
    }
    // URL
    const schemeEnd = path.indexOf(urlSchemeSeparator);
    if (schemeEnd !== -1) {
        const authorityStart = schemeEnd + urlSchemeSeparator.length;
        const authorityEnd = path.indexOf('/', authorityStart);
        if (authorityEnd !== -1) {
            // URL: "file:///", "file://server/", "file://server/path"
            // For local "file" URLs, include the leading DOS volume (if present).
            // Per https://www.ietf.org/rfc/rfc1738.txt, a host of "" or "localhost" is a
            // special case interpreted as "the machine from which the URL is being interpreted".
            const scheme = path.slice(0, schemeEnd);
            const authority = path.slice(authorityStart, authorityEnd);
            if (scheme === 'file' &&
                (authority === '' || authority === 'localhost') &&
                isVolumeCharacter(path.charCodeAt(authorityEnd + 1))) {
                const volumeSeparatorEnd = getFileUrlVolumeSeparatorEnd(path, authorityEnd + 2);
                if (volumeSeparatorEnd !== -1) {
                    if (path.charCodeAt(volumeSeparatorEnd) === 47 /* slash */) {
                        // URL: "file:///c:/", "file://localhost/c:/", "file:///c%3a/", "file://localhost/c%3a/"
                        return ~(volumeSeparatorEnd + 1);
                    }
                    if (volumeSeparatorEnd === path.length) {
                        // URL: "file:///c:", "file://localhost/c:", "file:///c$3a", "file://localhost/c%3a"
                        // but not "file:///c:d" or "file:///c%3ad"
                        return ~volumeSeparatorEnd;
                    }
                }
            }
            return ~(authorityEnd + 1); // URL: "file://server/", "http://server/"
        }
        return ~path.length; // URL: "file://server", "http://server"
    }
    // relative
    return 0;
};
const isVolumeCharacter = (charCode) => (charCode >= 97 /* a */ && charCode <= 122 /* z */) ||
    (charCode >= 65 /* A */ && charCode <= 90 /* Z */);
const getFileUrlVolumeSeparatorEnd = (url, start) => {
    const ch0 = url.charCodeAt(start);
    if (ch0 === 58 /* colon */)
        return start + 1;
    if (ch0 === 37 /* percent */ && url.charCodeAt(start + 1) === 51 /* _3 */) {
        const ch2 = url.charCodeAt(start + 2);
        if (ch2 === 97 /* a */ || ch2 === 65 /* A */)
            return start + 3;
    }
    return -1;
};
const pathComponents = (path, rootLength) => {
    const root = path.substring(0, rootLength);
    const rest = path.substring(rootLength).split('/');
    const restLen = rest.length;
    if (restLen > 0 && !rest[restLen - 1]) {
        rest.pop();
    }
    return [root, ...rest];
};

const validateComponentTag = (tag) => {
    if (tag !== tag.trim()) {
        return `Tag can not contain white spaces`;
    }
    if (tag !== tag.toLowerCase()) {
        return `Tag can not contain upper case characters`;
    }
    if (typeof tag !== 'string') {
        return `Tag "${tag}" must be a string type`;
    }
    if (tag.length === 0) {
        return `Received empty tag value`;
    }
    if (tag.indexOf(' ') > -1) {
        return `"${tag}" tag cannot contain a space`;
    }
    if (tag.indexOf(',') > -1) {
        return `"${tag}" tag cannot be used for multiple tags`;
    }
    const invalidChars = tag.replace(/\w|-/g, '');
    if (invalidChars !== '') {
        return `"${tag}" tag contains invalid characters: ${invalidChars}`;
    }
    if (tag.indexOf('-') === -1) {
        return `"${tag}" tag must contain a dash (-) to work as a valid web component`;
    }
    if (tag.indexOf('--') > -1) {
        return `"${tag}" tag cannot contain multiple dashes (--) next to each other`;
    }
    if (tag.indexOf('-') === 0) {
        return `"${tag}" tag cannot start with a dash (-)`;
    }
    if (tag.lastIndexOf('-') === tag.length - 1) {
        return `"${tag}" tag cannot end with a dash (-)`;
    }
    return undefined;
};

const parseFlags = (args, sys) => {
    const flags = {
        task: null,
        args: [],
        knownArgs: [],
        unknownArgs: null,
    };
    // cmd line has more priority over npm scripts cmd
    flags.args = args.slice();
    if (flags.args.length > 0 && flags.args[0] && !flags.args[0].startsWith('-')) {
        flags.task = flags.args[0];
    }
    parseArgs(flags, flags.args, flags.knownArgs);
    if (sys && sys.name === 'node') {
        const envArgs = getNpmConfigEnvArgs(sys);
        parseArgs(flags, envArgs, flags.knownArgs);
        envArgs.forEach((envArg) => {
            if (!flags.args.includes(envArg)) {
                flags.args.push(envArg);
            }
        });
    }
    if (flags.task != null) {
        const i = flags.args.indexOf(flags.task);
        if (i > -1) {
            flags.args.splice(i, 1);
        }
    }
    flags.unknownArgs = flags.args.filter((arg) => {
        return !flags.knownArgs.includes(arg);
    });
    return flags;
};
const parseArgs = (flags, args, knownArgs) => {
    ARG_OPTS.boolean.forEach((booleanName) => {
        const alias = ARG_OPTS.alias[booleanName];
        const flagKey = configCase(booleanName);
        if (typeof flags[flagKey] !== 'boolean') {
            flags[flagKey] = null;
        }
        args.forEach((cmdArg) => {
            if (cmdArg === `--${booleanName}`) {
                flags[flagKey] = true;
                knownArgs.push(cmdArg);
            }
            else if (cmdArg === `--${flagKey}`) {
                flags[flagKey] = true;
                knownArgs.push(cmdArg);
            }
            else if (cmdArg === `--no-${booleanName}`) {
                flags[flagKey] = false;
                knownArgs.push(cmdArg);
            }
            else if (cmdArg === `--no${dashToPascalCase(booleanName)}`) {
                flags[flagKey] = false;
                knownArgs.push(cmdArg);
            }
            else if (alias && cmdArg === `-${alias}`) {
                flags[flagKey] = true;
                knownArgs.push(cmdArg);
            }
        });
    });
    ARG_OPTS.string.forEach((stringName) => {
        const alias = ARG_OPTS.alias[stringName];
        const flagKey = configCase(stringName);
        if (typeof flags[flagKey] !== 'string') {
            flags[flagKey] = null;
        }
        for (let i = 0; i < args.length; i++) {
            const cmdArg = args[i];
            if (cmdArg.startsWith(`--${stringName}=`)) {
                const values = cmdArg.split('=');
                values.shift();
                flags[flagKey] = values.join('=');
                knownArgs.push(cmdArg);
            }
            else if (cmdArg === `--${stringName}`) {
                flags[flagKey] = args[i + 1];
                knownArgs.push(cmdArg);
                knownArgs.push(args[i + 1]);
            }
            else if (cmdArg === `--${flagKey}`) {
                flags[flagKey] = args[i + 1];
                knownArgs.push(cmdArg);
                knownArgs.push(args[i + 1]);
            }
            else if (cmdArg.startsWith(`--${flagKey}=`)) {
                const values = cmdArg.split('=');
                values.shift();
                flags[flagKey] = values.join('=');
                knownArgs.push(cmdArg);
            }
            else if (alias) {
                if (cmdArg.startsWith(`-${alias}=`)) {
                    const values = cmdArg.split('=');
                    values.shift();
                    flags[flagKey] = values.join('=');
                    knownArgs.push(cmdArg);
                }
                else if (cmdArg === `-${alias}`) {
                    flags[flagKey] = args[i + 1];
                    knownArgs.push(args[i + 1]);
                }
            }
        }
    });
    ARG_OPTS.number.forEach((numberName) => {
        const alias = ARG_OPTS.alias[numberName];
        const flagKey = configCase(numberName);
        if (typeof flags[flagKey] !== 'number') {
            flags[flagKey] = null;
        }
        for (let i = 0; i < args.length; i++) {
            const cmdArg = args[i];
            if (cmdArg.startsWith(`--${numberName}=`)) {
                const values = cmdArg.split('=');
                values.shift();
                flags[flagKey] = parseInt(values.join(''), 10);
                knownArgs.push(cmdArg);
            }
            else if (cmdArg === `--${numberName}`) {
                flags[flagKey] = parseInt(args[i + 1], 10);
                knownArgs.push(args[i + 1]);
            }
            else if (cmdArg.startsWith(`--${flagKey}=`)) {
                const values = cmdArg.split('=');
                values.shift();
                flags[flagKey] = parseInt(values.join(''), 10);
                knownArgs.push(cmdArg);
            }
            else if (cmdArg === `--${flagKey}`) {
                flags[flagKey] = parseInt(args[i + 1], 10);
                knownArgs.push(args[i + 1]);
            }
            else if (alias) {
                if (cmdArg.startsWith(`-${alias}=`)) {
                    const values = cmdArg.split('=');
                    values.shift();
                    flags[flagKey] = parseInt(values.join(''), 10);
                    knownArgs.push(cmdArg);
                }
                else if (cmdArg === `-${alias}`) {
                    flags[flagKey] = parseInt(args[i + 1], 10);
                    knownArgs.push(args[i + 1]);
                }
            }
        }
    });
};
const configCase = (prop) => {
    prop = dashToPascalCase(prop);
    return prop.charAt(0).toLowerCase() + prop.substr(1);
};
const ARG_OPTS = {
    boolean: [
        'build',
        'cache',
        'check-version',
        'ci',
        'compare',
        'debug',
        'dev',
        'devtools',
        'docs',
        'e2e',
        'es5',
        'esm',
        'headless',
        'help',
        'log',
        'open',
        'prerender',
        'prerender-external',
        'prod',
        'profile',
        'service-worker',
        'screenshot',
        'serve',
        'skip-node-check',
        'spec',
        'ssr',
        'stats',
        'update-screenshot',
        'verbose',
        'version',
        'watch',
    ],
    number: ['max-workers', 'port'],
    string: ['address', 'config', 'docs-json', 'emulate', 'log-level', 'root', 'screenshot-connector'],
    alias: {
        config: 'c',
        help: 'h',
        port: 'p',
        version: 'v',
    },
};
const getNpmConfigEnvArgs = (sys) => {
    // process.env.npm_config_argv
    // {"remain":["4444"],"cooked":["run","serve","--port","4444"],"original":["run","serve","--port","4444"]}
    let args = [];
    try {
        const npmConfigArgs = sys.getEnvironmentVar('npm_config_argv');
        if (npmConfigArgs) {
            args = JSON.parse(npmConfigArgs).original;
            if (args[0] === 'run') {
                args = args.slice(2);
            }
        }
    }
    catch (e) { }
    return args;
};

const dependencies = [
	{
		name: "@stencil/core",
		version: "2.10.0",
		main: "compiler/stencil.js",
		resources: [
			"package.json",
			"compiler/lib.d.ts",
			"compiler/lib.dom.d.ts",
			"compiler/lib.dom.iterable.d.ts",
			"compiler/lib.es2015.collection.d.ts",
			"compiler/lib.es2015.core.d.ts",
			"compiler/lib.es2015.d.ts",
			"compiler/lib.es2015.generator.d.ts",
			"compiler/lib.es2015.iterable.d.ts",
			"compiler/lib.es2015.promise.d.ts",
			"compiler/lib.es2015.proxy.d.ts",
			"compiler/lib.es2015.reflect.d.ts",
			"compiler/lib.es2015.symbol.d.ts",
			"compiler/lib.es2015.symbol.wellknown.d.ts",
			"compiler/lib.es2016.array.include.d.ts",
			"compiler/lib.es2016.d.ts",
			"compiler/lib.es2016.full.d.ts",
			"compiler/lib.es2017.d.ts",
			"compiler/lib.es2017.full.d.ts",
			"compiler/lib.es2017.intl.d.ts",
			"compiler/lib.es2017.object.d.ts",
			"compiler/lib.es2017.sharedmemory.d.ts",
			"compiler/lib.es2017.string.d.ts",
			"compiler/lib.es2017.typedarrays.d.ts",
			"compiler/lib.es2018.asyncgenerator.d.ts",
			"compiler/lib.es2018.asynciterable.d.ts",
			"compiler/lib.es2018.d.ts",
			"compiler/lib.es2018.full.d.ts",
			"compiler/lib.es2018.intl.d.ts",
			"compiler/lib.es2018.promise.d.ts",
			"compiler/lib.es2018.regexp.d.ts",
			"compiler/lib.es2019.array.d.ts",
			"compiler/lib.es2019.d.ts",
			"compiler/lib.es2019.full.d.ts",
			"compiler/lib.es2019.object.d.ts",
			"compiler/lib.es2019.string.d.ts",
			"compiler/lib.es2019.symbol.d.ts",
			"compiler/lib.es2020.bigint.d.ts",
			"compiler/lib.es2020.d.ts",
			"compiler/lib.es2020.full.d.ts",
			"compiler/lib.es2020.intl.d.ts",
			"compiler/lib.es2020.promise.d.ts",
			"compiler/lib.es2020.sharedmemory.d.ts",
			"compiler/lib.es2020.string.d.ts",
			"compiler/lib.es2020.symbol.wellknown.d.ts",
			"compiler/lib.es2021.d.ts",
			"compiler/lib.es2021.full.d.ts",
			"compiler/lib.es2021.promise.d.ts",
			"compiler/lib.es2021.string.d.ts",
			"compiler/lib.es2021.weakref.d.ts",
			"compiler/lib.es5.d.ts",
			"compiler/lib.es6.d.ts",
			"compiler/lib.esnext.d.ts",
			"compiler/lib.esnext.full.d.ts",
			"compiler/lib.esnext.intl.d.ts",
			"compiler/lib.esnext.promise.d.ts",
			"compiler/lib.esnext.string.d.ts",
			"compiler/lib.esnext.weakref.d.ts",
			"compiler/lib.scripthost.d.ts",
			"compiler/lib.webworker.d.ts",
			"compiler/lib.webworker.importscripts.d.ts",
			"compiler/lib.webworker.iterable.d.ts",
			"internal/index.d.ts",
			"internal/index.js",
			"internal/package.json",
			"internal/stencil-ext-modules.d.ts",
			"internal/stencil-private.d.ts",
			"internal/stencil-public-compiler.d.ts",
			"internal/stencil-public-docs.d.ts",
			"internal/stencil-public-runtime.d.ts",
			"mock-doc/index.js",
			"mock-doc/package.json",
			"internal/client/css-shim.js",
			"internal/client/dom.js",
			"internal/client/index.js",
			"internal/client/package.json",
			"internal/client/patch-browser.js",
			"internal/client/patch-esm.js",
			"internal/client/shadow-css.js",
			"internal/hydrate/index.js",
			"internal/hydrate/package.json",
			"internal/hydrate/runner.js",
			"internal/hydrate/shadow-css.js",
			"internal/stencil-core/index.d.ts",
			"internal/stencil-core/index.js"
		]
	},
	{
		name: "rollup",
		version: "2.42.3",
		main: "dist/es/rollup.browser.js"
	},
	{
		name: "terser",
		version: "5.6.1",
		main: "dist/bundle.min.js"
	},
	{
		name: "typescript",
		version: "4.3.5",
		main: "lib/typescript.js"
	}
];

const findConfig = async (opts) => {
    const sys = opts.sys;
    const cwd = sys.getCurrentDirectory();
    const results = {
        configPath: null,
        rootDir: normalizePath(cwd),
        diagnostics: [],
    };
    let configPath = opts.configPath;
    if (isString(configPath)) {
        if (!sys.platformPath.isAbsolute(configPath)) {
            // passed in a custom stencil config location
            // but it's relative, so prefix the cwd
            configPath = normalizePath(sys.platformPath.join(cwd, configPath));
        }
        else {
            // config path already an absolute path, we're good here
            configPath = normalizePath(opts.configPath);
        }
    }
    else {
        // nothing was passed in, use the current working directory
        configPath = results.rootDir;
    }
    const stat = await sys.stat(configPath);
    if (stat.error) {
        const diagnostic = buildError(results.diagnostics);
        diagnostic.absFilePath = configPath;
        diagnostic.header = `Invalid config path`;
        diagnostic.messageText = `Config path "${configPath}" not found`;
        return results;
    }
    if (stat.isFile) {
        results.configPath = configPath;
        results.rootDir = sys.platformPath.dirname(configPath);
    }
    else if (stat.isDirectory) {
        // this is only a directory, so let's make some assumptions
        for (const configName of ['stencil.config.ts', 'stencil.config.js']) {
            const testConfigFilePath = sys.platformPath.join(configPath, configName);
            const stat = await sys.stat(testConfigFilePath);
            if (stat.isFile) {
                results.configPath = testConfigFilePath;
                results.rootDir = sys.platformPath.dirname(testConfigFilePath);
                break;
            }
        }
    }
    return results;
};

const loadCoreCompiler = async (sys) => {
    await sys.dynamicImport(sys.getCompilerExecutingPath());
    return globalThis.stencil;
};

const startupLog = (logger, task) => {
    if (task === 'info' || task === 'serve' || task === 'version') {
        return;
    }
    logger.info(logger.cyan(`@stencil/core`));
};
const startupLogVersion = (logger, task, coreCompiler) => {
    if (task === 'info' || task === 'serve' || task === 'version') {
        return;
    }
    const isDevBuild = coreCompiler.version.includes('-dev.');
    let startupMsg;
    if (isDevBuild) {
        startupMsg = logger.yellow('[LOCAL DEV]');
    }
    else {
        startupMsg = logger.cyan(`v${coreCompiler.version}`);
    }
    startupMsg += logger.emoji(' ' + coreCompiler.vermoji);
    logger.info(startupMsg);
};
const loadedCompilerLog = (sys, logger, flags, coreCompiler) => {
    const sysDetails = sys.details;
    const runtimeInfo = `${sys.name} ${sys.version}`;
    const platformInfo = `${sysDetails.platform}, ${sysDetails.cpuModel}`;
    const statsInfo = `cpus: ${sys.hardwareConcurrency}, freemem: ${Math.round(sysDetails.freemem() / 1000000)}MB, totalmem: ${Math.round(sysDetails.totalmem / 1000000)}MB`;
    if (logger.getLevel() === 'debug') {
        logger.debug(runtimeInfo);
        logger.debug(platformInfo);
        logger.debug(statsInfo);
        logger.debug(`compiler: ${sys.getCompilerExecutingPath()}`);
        logger.debug(`build: ${coreCompiler.buildId}`);
    }
    else if (flags.ci) {
        logger.info(runtimeInfo);
        logger.info(platformInfo);
        logger.info(statsInfo);
    }
};
const startupCompilerLog = (coreCompiler, config) => {
    if (config.suppressLogs === true) {
        return;
    }
    const { logger } = config;
    const isDebug = logger.getLevel() === 'debug';
    const isPrerelease = coreCompiler.version.includes('-');
    const isDevBuild = coreCompiler.version.includes('-dev.');
    if (isPrerelease && !isDevBuild) {
        logger.warn(logger.yellow(`This is a prerelease build, undocumented changes might happen at any time. Technical support is not available for prereleases, but any assistance testing is appreciated.`));
    }
    if (config.devMode && !isDebug) {
        if (config.buildEs5) {
            logger.warn(`Generating ES5 during development is a very task expensive, initial and incremental builds will be much slower. Drop the '--es5' flag and use a modern browser for development.`);
        }
        if (!config.enableCache) {
            logger.warn(`Disabling cache during development will slow down incremental builds.`);
        }
    }
};

const taskPrerender = async (coreCompiler, config) => {
    startupCompilerLog(coreCompiler, config);
    const hydrateAppFilePath = config.flags.unknownArgs[0];
    if (typeof hydrateAppFilePath !== 'string') {
        config.logger.error(`Missing hydrate app script path`);
        return config.sys.exit(1);
    }
    const srcIndexHtmlPath = config.srcIndexHtml;
    const diagnostics = await runPrerenderTask(coreCompiler, config, hydrateAppFilePath, null, srcIndexHtmlPath);
    config.logger.printDiagnostics(diagnostics);
    if (diagnostics.some((d) => d.level === 'error')) {
        return config.sys.exit(1);
    }
};
const runPrerenderTask = async (coreCompiler, config, hydrateAppFilePath, componentGraph, srcIndexHtmlPath) => {
    const diagnostics = [];
    try {
        const prerenderer = await coreCompiler.createPrerenderer(config);
        const results = await prerenderer.start({
            hydrateAppFilePath,
            componentGraph,
            srcIndexHtmlPath,
        });
        diagnostics.push(...results.diagnostics);
    }
    catch (e) {
        catchError(diagnostics, e);
    }
    return diagnostics;
};

const startCheckVersion = async (config, currentVersion) => {
    if (config.devMode && !config.flags.ci && !currentVersion.includes('-dev.') && isFunction(config.sys.checkVersion)) {
        return config.sys.checkVersion(config.logger, currentVersion);
    }
    return null;
};
const printCheckVersionResults = async (versionChecker) => {
    if (versionChecker) {
        const checkVersionResults = await versionChecker;
        if (isFunction(checkVersionResults)) {
            checkVersionResults();
        }
    }
};

const taskWatch = async (coreCompiler, config) => {
    let devServer = null;
    let exitCode = 0;
    try {
        startupCompilerLog(coreCompiler, config);
        const versionChecker = startCheckVersion(config, coreCompiler.version);
        const compiler = await coreCompiler.createCompiler(config);
        const watcher = await compiler.createWatcher();
        if (config.flags.serve) {
            const devServerPath = config.sys.getDevServerExecutingPath();
            const { start } = await config.sys.dynamicImport(devServerPath);
            devServer = await start(config.devServer, config.logger, watcher);
        }
        config.sys.onProcessInterrupt(() => {
            config.logger.debug(`close watch`);
            compiler && compiler.destroy();
        });
        const rmVersionCheckerLog = watcher.on('buildFinish', async () => {
            // log the version check one time
            rmVersionCheckerLog();
            printCheckVersionResults(versionChecker);
        });
        if (devServer) {
            const rmDevServerLog = watcher.on('buildFinish', () => {
                // log the dev server url one time
                rmDevServerLog();
                config.logger.info(`${config.logger.cyan(devServer.browserUrl)}\n`);
            });
        }
        const closeResults = await watcher.start();
        if (closeResults.exitCode > 0) {
            exitCode = closeResults.exitCode;
        }
    }
    catch (e) {
        exitCode = 1;
        config.logger.error(e);
    }
    if (devServer) {
        await devServer.close();
    }
    if (exitCode > 0) {
        return config.sys.exit(exitCode);
    }
};

const tryFn = async (fn, ...args) => {
    try {
        return await fn(...args);
    }
    catch (_a) {
        // ignore
    }
    return null;
};
const isInteractive = (sys, config, object) => {
    var _a;
    const terminalInfo = object ||
        Object.freeze({
            tty: sys.isTTY() ? true : false,
            ci: ['CI', 'BUILD_ID', 'BUILD_NUMBER', 'BITBUCKET_COMMIT', 'CODEBUILD_BUILD_ARN'].filter((v) => !!sys.getEnvironmentVar(v)).length > 0 || !!((_a = config.flags) === null || _a === void 0 ? void 0 : _a.ci),
        });
    return terminalInfo.tty && !terminalInfo.ci;
};
const UUID_REGEX = new RegExp(/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i);
// Plucked from https://github.com/ionic-team/capacitor/blob/b893a57aaaf3a16e13db9c33037a12f1a5ac92e0/cli/src/util/uuid.ts
function uuidv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c == 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}
/**
 * Reads and parses a JSON file from the given `path`
 * @param sys The system where the command is invoked
 * @param path the path on the file system to read and parse
 * @returns the parsed JSON
 */
async function readJson(sys, path) {
    const file = await sys.readFile(path);
    return !!file && JSON.parse(file);
}
/**
 * Does the command have the debug flag?
 * @param config The config passed into the Stencil command
 * @returns true if --debug has been passed, otherwise false
 */
function hasDebug(config) {
    return config.flags.debug;
}
/**
 * Does the command have the verbose and debug flags?
 * @param config The config passed into the Stencil command
 * @returns true if both --debug and --verbose have been passed, otherwise false
 */
function hasVerbose(config) {
    return config.flags.verbose && hasDebug(config);
}

/**
 * Used to determine if tracking should occur.
 * @param config The config passed into the Stencil command
 * @param sys The system where the command is invoked
 * @param ci whether or not the process is running in a Continuous Integration (CI) environment
 * @returns true if telemetry should be sent, false otherwise
 */
async function shouldTrack(config, sys, ci) {
    return !ci && isInteractive(sys, config) && (await checkTelemetry(sys));
}

const isTest$1 = () => process.env.JEST_WORKER_ID !== undefined;
const defaultConfig = (sys) => sys.resolvePath(`${sys.homeDir()}/.ionic/${isTest$1() ? 'tmp-config.json' : 'config.json'}`);
const defaultConfigDirectory = (sys) => sys.resolvePath(`${sys.homeDir()}/.ionic`);
/**
 * Reads an Ionic configuration file from disk, parses it, and performs any necessary corrections to it if certain
 * values are deemed to be malformed
 * @param sys The system where the command is invoked
 * @returns the config read from disk that has been potentially been updated
 */
async function readConfig(sys) {
    let config = await readJson(sys, defaultConfig(sys));
    if (!config) {
        config = {
            'tokens.telemetry': uuidv4(),
            'telemetry.stencil': true,
        };
        await writeConfig(sys, config);
    }
    else if (!UUID_REGEX.test(config['tokens.telemetry'])) {
        const newUuid = uuidv4();
        await writeConfig(sys, { ...config, 'tokens.telemetry': newUuid });
        config['tokens.telemetry'] = newUuid;
    }
    return config;
}
/**
 * Writes an Ionic configuration file to disk.
 * @param sys The system where the command is invoked
 * @param config The config passed into the Stencil command
 * @returns boolean If the command was successful
 */
async function writeConfig(sys, config) {
    let result = false;
    try {
        await sys.createDir(defaultConfigDirectory(sys), { recursive: true });
        await sys.writeFile(defaultConfig(sys), JSON.stringify(config, null, 2));
        result = true;
    }
    catch (error) {
        console.error(`Stencil Telemetry: couldn't write configuration file to ${defaultConfig(sys)} - ${error}.`);
    }
    return result;
}
/**
 * Update a subset of the Ionic config.
 * @param sys The system where the command is invoked
 * @param newOptions The new options to save
 * @returns boolean If the command was successful
 */
async function updateConfig(sys, newOptions) {
    const config = await readConfig(sys);
    return await writeConfig(sys, Object.assign(config, newOptions));
}

const isOutputTargetDocs = (o) => o.type === DOCS_README || o.type === DOCS_JSON || o.type === DOCS_CUSTOM || o.type === DOCS_VSCODE;
const DOCS_CUSTOM = 'docs-custom';
const DOCS_JSON = `docs-json`;
const DOCS_README = `docs-readme`;
const DOCS_VSCODE = `docs-vscode`;
const WWW = `www`;

/**
 * Used to within taskBuild to provide the component_count property.
 *
 * @param sys The system where the command is invoked
 * @param config The config passed into the Stencil command
 * @param logger The tool used to do logging
 * @param coreCompiler The compiler used to do builds
 * @param result The results of a compiler build.
 */
async function telemetryBuildFinishedAction(sys, config, logger, coreCompiler, result) {
    const tracking = await shouldTrack(config, sys, config.flags.ci);
    if (!tracking) {
        return;
    }
    const component_count = Object.keys(result.componentGraph).length;
    const data = await prepareData(coreCompiler, config, sys, result.duration, component_count);
    await sendMetric(sys, config, 'stencil_cli_command', data);
    logger.debug(`${logger.blue('Telemetry')}: ${logger.gray(JSON.stringify(data))}`);
}
/**
 * A function to wrap a compiler task function around. Will send telemetry if, and only if, the machine allows.
 * @param sys The system where the command is invoked
 * @param config The config passed into the Stencil command
 * @param logger The tool used to do logging
 * @param coreCompiler The compiler used to do builds
 * @param action A Promise-based function to call in order to get the duration of any given command.
 * @returns void
 */
async function telemetryAction(sys, config, logger, coreCompiler, action) {
    var _a;
    const tracking = await shouldTrack(config, sys, !!((_a = config === null || config === void 0 ? void 0 : config.flags) === null || _a === void 0 ? void 0 : _a.ci));
    let duration = undefined;
    let error;
    if (action) {
        const start = new Date();
        try {
            await action();
        }
        catch (e) {
            error = e;
        }
        const end = new Date();
        duration = end.getTime() - start.getTime();
    }
    // We'll get componentCount details inside the taskBuild, so let's not send two messages.
    if (!tracking || (config.flags.task == 'build' && !config.flags.args.includes('--watch'))) {
        return;
    }
    const data = await prepareData(coreCompiler, config, sys, duration);
    await sendMetric(sys, config, 'stencil_cli_command', data);
    logger.debug(`${logger.blue('Telemetry')}: ${logger.gray(JSON.stringify(data))}`);
    if (error) {
        throw error;
    }
}
function hasAppTarget(config) {
    return config.outputTargets.some((target) => target.type === WWW && (!!target.serviceWorker || (!!target.baseUrl && target.baseUrl !== '/')));
}
function isUsingYarn(sys) {
    var _a;
    return ((_a = sys.getEnvironmentVar('npm_execpath')) === null || _a === void 0 ? void 0 : _a.includes('yarn')) || false;
}
async function getActiveTargets(config) {
    const result = config.outputTargets.map((t) => t.type);
    return Array.from(new Set(result));
}
const prepareData = async (coreCompiler, config, sys, duration_ms, component_count = undefined) => {
    const { typescript, rollup } = coreCompiler.versions || { typescript: 'unknown', rollup: 'unknown' };
    const { packages, packagesNoVersions } = await getInstalledPackages(sys, config);
    const targets = await getActiveTargets(config);
    const yarn = isUsingYarn(sys);
    const stencil = coreCompiler.version || 'unknown';
    const system = `${sys.name} ${sys.version}`;
    const os_name = sys.details.platform;
    const os_version = sys.details.release;
    const cpu_model = sys.details.cpuModel;
    const build = coreCompiler.buildId || 'unknown';
    const has_app_pwa_config = hasAppTarget(config);
    return {
        yarn,
        duration_ms,
        component_count,
        targets,
        packages,
        packages_no_versions: packagesNoVersions,
        arguments: config.flags.args,
        task: config.flags.task,
        stencil,
        system,
        system_major: getMajorVersion(system),
        os_name,
        os_version,
        cpu_model,
        build,
        typescript,
        rollup,
        has_app_pwa_config,
    };
};
/**
 * Reads package-lock.json, yarn.lock, and package.json files in order to cross reference
 * the dependencies and devDependencies properties. Pulls up the current installed version
 * of each package under the @stencil, @ionic, and @capacitor scopes.
 * @returns string[]
 */
async function getInstalledPackages(sys, config) {
    let packages = [];
    let packagesNoVersions = [];
    const yarn = isUsingYarn(sys);
    try {
        // Read package.json and package-lock.json
        const appRootDir = sys.getCurrentDirectory();
        const packageJson = await tryFn(readJson, sys, sys.resolvePath(appRootDir + '/package.json'));
        // They don't have a package.json for some reason? Eject button.
        if (!packageJson) {
            return { packages, packagesNoVersions };
        }
        const rawPackages = Object.entries({
            ...packageJson.devDependencies,
            ...packageJson.dependencies,
        });
        // Collect packages only in the stencil, ionic, or capacitor org's:
        // https://www.npmjs.com/org/stencil
        const ionicPackages = rawPackages.filter(([k]) => k.startsWith('@stencil/') || k.startsWith('@ionic/') || k.startsWith('@capacitor/'));
        try {
            packages = yarn ? await yarnPackages(sys, ionicPackages) : await npmPackages(sys, ionicPackages);
        }
        catch (e) {
            packages = ionicPackages.map(([k, v]) => `${k}@${v.replace('^', '')}`);
        }
        packagesNoVersions = ionicPackages.map(([k]) => `${k}`);
        return { packages, packagesNoVersions };
    }
    catch (err) {
        hasDebug(config) && console.error(err);
        return { packages, packagesNoVersions };
    }
}
/**
 * Visits the npm lock file to find the exact versions that are installed
 * @param sys The system where the command is invoked
 * @param ionicPackages a list of the found packages matching `@stencil`, `@capacitor`, or `@ionic` from the package.json file.
 * @returns an array of strings of all the packages and their versions.
 */
async function npmPackages(sys, ionicPackages) {
    const appRootDir = sys.getCurrentDirectory();
    const packageLockJson = await tryFn(readJson, sys, sys.resolvePath(appRootDir + '/package-lock.json'));
    return ionicPackages.map(([k, v]) => {
        var _a, _b, _c, _d;
        let version = (_d = (_b = (_a = packageLockJson === null || packageLockJson === void 0 ? void 0 : packageLockJson.dependencies[k]) === null || _a === void 0 ? void 0 : _a.version) !== null && _b !== void 0 ? _b : (_c = packageLockJson === null || packageLockJson === void 0 ? void 0 : packageLockJson.devDependencies[k]) === null || _c === void 0 ? void 0 : _c.version) !== null && _d !== void 0 ? _d : v;
        version = version.includes('file:') ? sanitizeDeclaredVersion(v) : version;
        return `${k}@${version}`;
    });
}
/**
 * Visits the yarn lock file to find the exact versions that are installed
 * @param sys The system where the command is invoked
 * @param ionicPackages a list of the found packages matching `@stencil`, `@capacitor`, or `@ionic` from the package.json file.
 * @returns an array of strings of all the packages and their versions.
 */
async function yarnPackages(sys, ionicPackages) {
    const appRootDir = sys.getCurrentDirectory();
    const yarnLock = sys.readFileSync(sys.resolvePath(appRootDir + '/yarn.lock'));
    const yarnLockYml = sys.parseYarnLockFile(yarnLock);
    return ionicPackages.map(([k, v]) => {
        var _a;
        const identifiedVersion = `${k}@${v}`;
        let version = (_a = yarnLockYml.object[identifiedVersion]) === null || _a === void 0 ? void 0 : _a.version;
        version = version.includes('undefined') ? sanitizeDeclaredVersion(identifiedVersion) : version;
        return `${k}@${version}`;
    });
}
/**
 * This function is used for fallback purposes, where an npm or yarn lock file doesn't exist in the consumers directory.
 * This will strip away '*', '^' and '~' from the declared package versions in a package.json.
 * @param version the raw semver pattern identifier version string
 * @returns a cleaned up representation without any qualifiers
 */
function sanitizeDeclaredVersion(version) {
    return version.replace(/[*^~]/g, '');
}
/**
 * If telemetry is enabled, send a metric via IPC to a forked process for uploading.
 */
async function sendMetric(sys, config, name, value) {
    const session_id = await getTelemetryToken(sys);
    const message = {
        name,
        timestamp: new Date().toISOString(),
        source: 'stencil_cli',
        value,
        session_id,
    };
    await sendTelemetry(sys, config, { type: 'telemetry', message });
}
/**
 * Used to read the config file's tokens.telemetry property.
 * @param sys The system where the command is invoked
 * @returns string
 */
async function getTelemetryToken(sys) {
    const config = await readConfig(sys);
    if (config['tokens.telemetry'] === undefined) {
        config['tokens.telemetry'] = uuidv4();
        await writeConfig(sys, config);
    }
    return config['tokens.telemetry'];
}
/**
 * Issues a request to the telemetry server.
 * @param sys The system where the command is invoked
 * @param config The config passed into the Stencil command
 * @param data Data to be tracked
 */
async function sendTelemetry(sys, config, data) {
    try {
        const now = new Date().toISOString();
        const body = {
            metrics: [data.message],
            sent_at: now,
        };
        // This request is only made if telemetry is on.
        const response = await sys.fetch('https://api.ionicjs.com/events/metrics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        });
        hasVerbose(config) &&
            console.debug('\nSent %O metric to events service (status: %O)', data.message.name, response.status, '\n');
        if (response.status !== 204) {
            hasVerbose(config) &&
                console.debug('\nBad response from events service. Request body: %O', response.body.toString(), '\n');
        }
    }
    catch (e) {
        hasVerbose(config) && console.debug('Telemetry request failed:', e);
    }
}
/**
 * Checks if telemetry is enabled on this machine
 * @param sys The system where the command is invoked
 * @returns true if telemetry is enabled, false otherwise
 */
async function checkTelemetry(sys) {
    const config = await readConfig(sys);
    if (config['telemetry.stencil'] === undefined) {
        config['telemetry.stencil'] = true;
        await writeConfig(sys, config);
    }
    return config['telemetry.stencil'];
}
/**
 * Writes to the config file, enabling telemetry for this machine.
 * @param sys The system where the command is invoked
 * @returns true if writing the file was successful, false otherwise
 */
async function enableTelemetry(sys) {
    return await updateConfig(sys, { 'telemetry.stencil': true });
}
/**
 * Writes to the config file, disabling telemetry for this machine.
 * @param sys The system where the command is invoked
 * @returns true if writing the file was successful, false otherwise
 */
async function disableTelemetry(sys) {
    return await updateConfig(sys, { 'telemetry.stencil': false });
}
/**
 * Takes in a semver string in order to return the major version.
 * @param version The fully qualified semver version
 * @returns a string of the major version
 */
function getMajorVersion(version) {
    const parts = version.split('.');
    return parts[0];
}

const taskBuild = async (coreCompiler, config, sys) => {
    if (config.flags.watch) {
        // watch build
        await taskWatch(coreCompiler, config);
        return;
    }
    // one-time build
    let exitCode = 0;
    try {
        startupCompilerLog(coreCompiler, config);
        const versionChecker = startCheckVersion(config, coreCompiler.version);
        const compiler = await coreCompiler.createCompiler(config);
        const results = await compiler.build();
        // TODO(STENCIL-148) make this parameter no longer optional, remove the surrounding if statement
        if (sys) {
            await telemetryBuildFinishedAction(sys, config, config.logger, coreCompiler, results);
        }
        await compiler.destroy();
        if (results.hasError) {
            exitCode = 1;
        }
        else if (config.flags.prerender) {
            const prerenderDiagnostics = await runPrerenderTask(coreCompiler, config, results.hydrateAppFilePath, results.componentGraph, null);
            config.logger.printDiagnostics(prerenderDiagnostics);
            if (prerenderDiagnostics.some((d) => d.level === 'error')) {
                exitCode = 1;
            }
        }
        await printCheckVersionResults(versionChecker);
    }
    catch (e) {
        exitCode = 1;
        config.logger.error(e);
    }
    if (exitCode > 0) {
        return config.sys.exit(exitCode);
    }
};

const taskDocs = async (coreCompiler, config) => {
    config.devServer = null;
    config.outputTargets = config.outputTargets.filter(isOutputTargetDocs);
    config.devMode = true;
    startupCompilerLog(coreCompiler, config);
    const compiler = await coreCompiler.createCompiler(config);
    await compiler.build();
    await compiler.destroy();
};

const IS_NODE_ENV = typeof global !== 'undefined' &&
    typeof require === 'function' &&
    !!global.process &&
    typeof __filename === 'string' &&
    (!global.origin || typeof global.origin !== 'string');

/**
 * Task to generate component boilerplate.
 */
const taskGenerate = async (coreCompiler, config) => {
    if (!IS_NODE_ENV) {
        config.logger.error(`"generate" command is currently only implemented for a NodeJS environment`);
        return config.sys.exit(1);
    }
    const path = coreCompiler.path;
    if (!config.configPath) {
        config.logger.error('Please run this command in your root directory (i. e. the one containing stencil.config.ts).');
        return config.sys.exit(1);
    }
    const absoluteSrcDir = config.srcDir;
    if (!absoluteSrcDir) {
        config.logger.error(`Stencil's srcDir was not specified.`);
        return config.sys.exit(1);
    }
    const { prompt } = await Promise.resolve().then(function () { return /*#__PURE__*/_interopNamespace(require('../sys/node/prompts.js')); });
    const input = config.flags.unknownArgs.find((arg) => !arg.startsWith('-')) ||
        (await prompt({ name: 'tagName', type: 'text', message: 'Component tag name (dash-case):' })).tagName;
    const { dir, base: componentName } = path.parse(input);
    const tagError = validateComponentTag(componentName);
    if (tagError) {
        config.logger.error(tagError);
        return config.sys.exit(1);
    }
    const extensionsToGenerate = ['tsx', ...(await chooseFilesToGenerate())];
    const testFolder = extensionsToGenerate.some(isTest) ? 'test' : '';
    const outDir = path.join(absoluteSrcDir, 'components', dir, componentName);
    await config.sys.createDir(path.join(outDir, testFolder), { recursive: true });
    const writtenFiles = await Promise.all(extensionsToGenerate.map((extension) => writeFileByExtension(coreCompiler, config, outDir, componentName, extension, extensionsToGenerate.includes('css')))).catch((error) => config.logger.error(error));
    if (!writtenFiles) {
        return config.sys.exit(1);
    }
    console.log();
    console.log(`${config.logger.gray('$')} stencil generate ${input}`);
    console.log();
    console.log(config.logger.bold('The following files have been generated:'));
    const absoluteRootDir = config.rootDir;
    writtenFiles.map((file) => console.log(`  - ${path.relative(absoluteRootDir, file)}`));
};
/**
 * Show a checkbox prompt to select the files to be generated.
 */
const chooseFilesToGenerate = async () => {
    const { prompt } = await Promise.resolve().then(function () { return /*#__PURE__*/_interopNamespace(require('../sys/node/prompts.js')); });
    return (await prompt({
        name: 'filesToGenerate',
        type: 'multiselect',
        message: 'Which additional files do you want to generate?',
        choices: [
            { value: 'css', title: 'Stylesheet (.css)', selected: true },
            { value: 'spec.tsx', title: 'Spec Test  (.spec.tsx)', selected: true },
            { value: 'e2e.ts', title: 'E2E Test (.e2e.ts)', selected: true },
        ],
    })).filesToGenerate;
};
/**
 * Get a file's boilerplate by its extension and write it to disk.
 */
const writeFileByExtension = async (coreCompiler, config, path, name, extension, withCss) => {
    if (isTest(extension)) {
        path = coreCompiler.path.join(path, 'test');
    }
    const outFile = coreCompiler.path.join(path, `${name}.${extension}`);
    const boilerplate = getBoilerplateByExtension(name, extension, withCss);
    await config.sys.writeFile(outFile, boilerplate);
    return outFile;
};
const isTest = (extension) => {
    return extension === 'e2e.ts' || extension === 'spec.tsx';
};
/**
 * Get the boilerplate for a file by its extension.
 */
const getBoilerplateByExtension = (tagName, extension, withCss) => {
    switch (extension) {
        case 'tsx':
            return getComponentBoilerplate(tagName, withCss);
        case 'css':
            return getStyleUrlBoilerplate();
        case 'spec.tsx':
            return getSpecTestBoilerplate(tagName);
        case 'e2e.ts':
            return getE2eTestBoilerplate(tagName);
        default:
            throw new Error(`Unkown extension "${extension}".`);
    }
};
/**
 * Get the boilerplate for a component.
 */
const getComponentBoilerplate = (tagName, hasStyle) => {
    const decorator = [`{`];
    decorator.push(`  tag: '${tagName}',`);
    if (hasStyle) {
        decorator.push(`  styleUrl: '${tagName}.css',`);
    }
    decorator.push(`  shadow: true,`);
    decorator.push(`}`);
    return `import { Component, Host, h } from '@stencil/core';

@Component(${decorator.join('\n')})
export class ${toPascalCase(tagName)} {

  render() {
    return (
      <Host>
        <slot></slot>
      </Host>
    );
  }

}
`;
};
/**
 * Get the boilerplate for style.
 */
const getStyleUrlBoilerplate = () => `:host {
  display: block;
}
`;
/**
 * Get the boilerplate for a spec test.
 */
const getSpecTestBoilerplate = (tagName) => `import { newSpecPage } from '@stencil/core/testing';
import { ${toPascalCase(tagName)} } from '../${tagName}';

describe('${tagName}', () => {
  it('renders', async () => {
    const page = await newSpecPage({
      components: [${toPascalCase(tagName)}],
      html: \`<${tagName}></${tagName}>\`,
    });
    expect(page.root).toEqualHtml(\`
      <${tagName}>
        <mock:shadow-root>
          <slot></slot>
        </mock:shadow-root>
      </${tagName}>
    \`);
  });
});
`;
/**
 * Get the boilerplate for an E2E test.
 */
const getE2eTestBoilerplate = (name) => `import { newE2EPage } from '@stencil/core/testing';

describe('${name}', () => {
  it('renders', async () => {
    const page = await newE2EPage();
    await page.setContent('<${name}></${name}>');

    const element = await page.find('${name}');
    expect(element).toHaveClass('hydrated');
  });
});
`;
/**
 * Convert a dash case string to pascal case.
 */
const toPascalCase = (str) => str.split('-').reduce((res, part) => res + part[0].toUpperCase() + part.substr(1), '');

const taskTelemetry = async (config, sys, logger) => {
    const prompt = logger.dim(sys.details.platform === 'windows' ? '>' : '$');
    const isEnabling = config.flags.args.includes('on');
    const isDisabling = config.flags.args.includes('off');
    const INFORMATION = `Opt in or our of telemetry. Information about the data we collect is available on our website: ${logger.bold('https://stenciljs.com/telemetry')}`;
    const THANK_YOU = `Thank you for helping to make Stencil better! 💖`;
    const ENABLED_MESSAGE = `${logger.green('Enabled')}. ${THANK_YOU}\n\n`;
    const DISABLED_MESSAGE = `${logger.red('Disabled')}\n\n`;
    const hasTelemetry = await checkTelemetry(sys);
    if (isEnabling) {
        const result = await enableTelemetry(sys);
        result
            ? console.log(`\n  ${logger.bold('Telemetry is now ') + ENABLED_MESSAGE}`)
            : console.log(`Something went wrong when enabling Telemetry.`);
        return;
    }
    if (isDisabling) {
        const result = await disableTelemetry(sys);
        result
            ? console.log(`\n  ${logger.bold('Telemetry is now ') + DISABLED_MESSAGE}`)
            : console.log(`Something went wrong when disabling Telemetry.`);
        return;
    }
    console.log(`  ${logger.bold('Telemetry:')} ${logger.dim(INFORMATION)}`);
    console.log(`\n  ${logger.bold('Status')}: ${hasTelemetry ? ENABLED_MESSAGE : DISABLED_MESSAGE}`);
    console.log(`    ${prompt} ${logger.green('stencil telemetry [off|on]')}

        ${logger.cyan('off')} ${logger.dim('.............')} Disable sharing anonymous usage data
        ${logger.cyan('on')} ${logger.dim('..............')} Enable sharing anonymous usage data
  `);
};

const taskHelp = async (config, logger, sys) => {
    const prompt = logger.dim(sys.details.platform === 'windows' ? '>' : '$');
    console.log(`
  ${logger.bold('Build:')} ${logger.dim('Build components for development or production.')}

    ${prompt} ${logger.green('stencil build [--dev] [--watch] [--prerender] [--debug]')}

      ${logger.cyan('--dev')} ${logger.dim('.............')} Development build
      ${logger.cyan('--watch')} ${logger.dim('...........')} Rebuild when files update
      ${logger.cyan('--serve')} ${logger.dim('...........')} Start the dev-server
      ${logger.cyan('--prerender')} ${logger.dim('.......')} Prerender the application
      ${logger.cyan('--docs')} ${logger.dim('............')} Generate component readme.md docs
      ${logger.cyan('--config')} ${logger.dim('..........')} Set stencil config file
      ${logger.cyan('--stats')} ${logger.dim('...........')} Write stencil-stats.json file
      ${logger.cyan('--log')} ${logger.dim('.............')} Write stencil-build.log file
      ${logger.cyan('--debug')} ${logger.dim('...........')} Set the log level to debug


  ${logger.bold('Test:')} ${logger.dim('Run unit and end-to-end tests.')}

    ${prompt} ${logger.green('stencil test [--spec] [--e2e]')}

      ${logger.cyan('--spec')} ${logger.dim('............')} Run unit tests with Jest
      ${logger.cyan('--e2e')} ${logger.dim('.............')} Run e2e tests with Puppeteer


  ${logger.bold('Generate:')} ${logger.dim('Bootstrap components.')}

    ${prompt} ${logger.green('stencil generate')} or ${logger.green('stencil g')}

`);
    // TODO(STENCIL-148) make this parameter no longer optional, remove the surrounding if statement
    if (sys) {
        await taskTelemetry(config, sys, logger);
    }
    console.log(`
  ${logger.bold('Examples:')}

  ${prompt} ${logger.green('stencil build --dev --watch --serve')}
  ${prompt} ${logger.green('stencil build --prerender')}
  ${prompt} ${logger.green('stencil test --spec --e2e')}
  ${prompt} ${logger.green('stencil telemetry on')}
  ${prompt} ${logger.green('stencil generate')}
  ${prompt} ${logger.green('stencil g my-component')}
`);
};

const taskInfo = (coreCompiler, sys, logger) => {
    const details = sys.details;
    const versions = coreCompiler.versions;
    console.log(``);
    console.log(`${logger.cyan('      System:')} ${sys.name} ${sys.version}`);
    console.log(`${logger.cyan('     Plaform:')} ${details.platform} (${details.release})`);
    console.log(`${logger.cyan('   CPU Model:')} ${details.cpuModel} (${sys.hardwareConcurrency} cpu${sys.hardwareConcurrency !== 1 ? 's' : ''})`);
    console.log(`${logger.cyan('    Compiler:')} ${sys.getCompilerExecutingPath()}`);
    console.log(`${logger.cyan('       Build:')} ${coreCompiler.buildId}`);
    console.log(`${logger.cyan('     Stencil:')} ${coreCompiler.version}${logger.emoji(' ' + coreCompiler.vermoji)}`);
    console.log(`${logger.cyan('  TypeScript:')} ${versions.typescript}`);
    console.log(`${logger.cyan('      Rollup:')} ${versions.rollup}`);
    console.log(`${logger.cyan('      Parse5:')} ${versions.parse5}`);
    console.log(`${logger.cyan('      Sizzle:')} ${versions.sizzle}`);
    console.log(`${logger.cyan('      Terser:')} ${versions.terser}`);
    console.log(``);
};

const taskServe = async (config) => {
    config.suppressLogs = true;
    config.flags.serve = true;
    config.devServer.openBrowser = config.flags.open;
    config.devServer.reloadStrategy = null;
    config.devServer.initialLoadUrl = '/';
    config.devServer.websocket = false;
    config.maxConcurrentWorkers = 1;
    config.devServer.root = isString(config.flags.root) ? config.flags.root : config.sys.getCurrentDirectory();
    const devServerPath = config.sys.getDevServerExecutingPath();
    const { start } = await config.sys.dynamicImport(devServerPath);
    const devServer = await start(config.devServer, config.logger);
    console.log(`${config.logger.cyan('     Root:')} ${devServer.root}`);
    console.log(`${config.logger.cyan('  Address:')} ${devServer.address}`);
    console.log(`${config.logger.cyan('     Port:')} ${devServer.port}`);
    console.log(`${config.logger.cyan('   Server:')} ${devServer.browserUrl}`);
    console.log(``);
    config.sys.onProcessInterrupt(() => {
        if (devServer) {
            config.logger.debug(`dev server close: ${devServer.browserUrl}`);
            devServer.close();
        }
    });
};

const taskTest = async (config) => {
    if (!IS_NODE_ENV) {
        config.logger.error(`"test" command is currently only implemented for a NodeJS environment`);
        return config.sys.exit(1);
    }
    try {
        config.buildDocs = false;
        const testingRunOpts = {
            e2e: !!config.flags.e2e,
            screenshot: !!config.flags.screenshot,
            spec: !!config.flags.spec,
            updateScreenshot: !!config.flags.updateScreenshot,
        };
        // always ensure we have jest modules installed
        const ensureModuleIds = ['@types/jest', 'jest', 'jest-cli'];
        if (testingRunOpts.e2e) {
            // if it's an e2e test, also make sure we're got
            // puppeteer modules installed and if browserExecutablePath is provided don't download Chromium use only puppeteer-core instead
            const puppeteer = config.testing.browserExecutablePath ? 'puppeteer-core' : 'puppeteer';
            ensureModuleIds.push(puppeteer);
            if (testingRunOpts.screenshot) {
                // ensure we've got pixelmatch for screenshots
                config.logger.warn(config.logger.yellow(`EXPERIMENTAL: screenshot visual diff testing is currently under heavy development and has not reached a stable status. However, any assistance testing would be appreciated.`));
            }
        }
        // ensure we've got the required modules installed
        const diagnostics = await config.sys.lazyRequire.ensure(config.rootDir, ensureModuleIds);
        if (diagnostics.length > 0) {
            config.logger.printDiagnostics(diagnostics);
            return config.sys.exit(1);
        }
        // let's test!
        const { createTesting } = await Promise.resolve().then(function () { return /*#__PURE__*/_interopNamespace(require('../testing/index.js')); });
        const testing = await createTesting(config);
        const passed = await testing.run(testingRunOpts);
        await testing.destroy();
        if (!passed) {
            return config.sys.exit(1);
        }
    }
    catch (e) {
        config.logger.error(e);
        return config.sys.exit(1);
    }
};

const run = async (init) => {
    const { args, logger, sys } = init;
    try {
        const flags = parseFlags(args, sys);
        const task = flags.task;
        if (flags.debug || flags.verbose) {
            logger.setLevel('debug');
        }
        if (flags.ci) {
            logger.enableColors(false);
        }
        if (isFunction(sys.applyGlobalPatch)) {
            sys.applyGlobalPatch(sys.getCurrentDirectory());
        }
        if (task === 'help' || flags.help) {
            await taskHelp({ flags: { task: 'help', args }, outputTargets: [] }, logger, sys);
            return;
        }
        startupLog(logger, task);
        const findConfigResults = await findConfig({ sys, configPath: flags.config });
        if (hasError(findConfigResults.diagnostics)) {
            logger.printDiagnostics(findConfigResults.diagnostics);
            return sys.exit(1);
        }
        const ensureDepsResults = await sys.ensureDependencies({
            rootDir: findConfigResults.rootDir,
            logger,
            dependencies: dependencies,
        });
        if (hasError(ensureDepsResults.diagnostics)) {
            logger.printDiagnostics(ensureDepsResults.diagnostics);
            return sys.exit(1);
        }
        const coreCompiler = await loadCoreCompiler(sys);
        if (task === 'version' || flags.version) {
            console.log(coreCompiler.version);
            return;
        }
        startupLogVersion(logger, task, coreCompiler);
        loadedCompilerLog(sys, logger, flags, coreCompiler);
        if (task === 'info') {
            await telemetryAction(sys, { flags: { task: 'info' }, outputTargets: [] }, logger, coreCompiler, async () => {
                await taskInfo(coreCompiler, sys, logger);
            });
            return;
        }
        const validated = await coreCompiler.loadConfig({
            config: {
                flags,
            },
            configPath: findConfigResults.configPath,
            logger,
            sys,
        });
        if (validated.diagnostics.length > 0) {
            logger.printDiagnostics(validated.diagnostics);
            if (hasError(validated.diagnostics)) {
                return sys.exit(1);
            }
        }
        if (isFunction(sys.applyGlobalPatch)) {
            sys.applyGlobalPatch(validated.config.rootDir);
        }
        await sys.ensureResources({ rootDir: validated.config.rootDir, logger, dependencies: dependencies });
        await telemetryAction(sys, validated.config, logger, coreCompiler, async () => {
            await runTask(coreCompiler, validated.config, task, sys);
        });
    }
    catch (e) {
        if (!shouldIgnoreError(e)) {
            logger.error(`uncaught cli error: ${e}${logger.getLevel() === 'debug' ? e.stack : ''}`);
            return sys.exit(1);
        }
    }
};
const runTask = async (coreCompiler, config, task, sys) => {
    config.flags = config.flags || { task };
    config.outputTargets = config.outputTargets || [];
    switch (task) {
        case 'build':
            await taskBuild(coreCompiler, config, sys);
            break;
        case 'docs':
            await taskDocs(coreCompiler, config);
            break;
        case 'generate':
        case 'g':
            await taskGenerate(coreCompiler, config);
            break;
        case 'help':
            taskHelp(config, config.logger, sys);
            break;
        case 'prerender':
            await taskPrerender(coreCompiler, config);
            break;
        case 'serve':
            await taskServe(config);
            break;
        case 'telemetry':
            // TODO(STENCIL-148) make this parameter no longer optional, remove the surrounding if statement
            if (sys) {
                await taskTelemetry(config, sys, config.logger);
            }
            break;
        case 'test':
            await taskTest(config);
            break;
        case 'version':
            console.log(coreCompiler.version);
            break;
        default:
            config.logger.error(`${config.logger.emoji('❌ ')}Invalid stencil command, please see the options below:`);
            taskHelp(config, config.logger, sys);
            return config.sys.exit(1);
    }
};

exports.parseFlags = parseFlags;
exports.run = run;
exports.runTask = runTask;
//# sourceMappingURL=index.cjs.map
