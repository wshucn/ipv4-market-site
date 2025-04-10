/*!
 Stencil Node System v2.10.0 | MIT Licensed | https://stenciljs.com
 */
function _interopDefaultLegacy(e) {
 return e && "object" == typeof e && "default" in e ? e : {
  default: e
 };
}

function _interopNamespace(e) {
 if (e && e.__esModule) return e;
 var t = Object.create(null);
 return e && Object.keys(e).forEach((function(r) {
  if ("default" !== r) {
   var n = Object.getOwnPropertyDescriptor(e, r);
   Object.defineProperty(t, r, n.get ? n : {
    enumerable: !0,
    get: function() {
     return e[r];
    }
   });
  }
 })), t.default = e, t;
}

function createCommonjsModule(e, t, r) {
 return e(r = {
  path: t,
  exports: {},
  require: function(e, t) {
   return function r() {
    throw new Error("Dynamic requires are not currently supported by @rollup/plugin-commonjs");
   }();
  }
 }, r.exports), r.exports;
}

async function nodeCopyTasks(e, t) {
 const r = {
  diagnostics: [],
  dirPaths: [],
  filePaths: []
 };
 try {
  i = await Promise.all(e.map((e => async function r(e, t) {
   return (e => {
    const t = {
     "{": "}",
     "(": ")",
     "[": "]"
    }, r = /\\(.)|(^!|\*|[\].+)]\?|\[[^\\\]]+\]|\{[^\\}]+\}|\(\?[:!=][^\\)]+\)|\([^|]+\|[^\\)]+\))/;
    if ("" === e) return !1;
    let n;
    for (;n = r.exec(e); ) {
     if (n[2]) return !0;
     let r = n.index + n[0].length;
     const i = n[1], o = i ? t[i] : null;
     if (i && o) {
      const t = e.indexOf(o, r);
      -1 !== t && (r = t + 1);
     }
     e = e.slice(r);
    }
    return !1;
   })(e.src) ? await async function r(e, t) {
    return (await asyncGlob(e.src, {
     cwd: t,
     nodir: !0
    })).map((r => function n(e, t, r) {
     const n = path__default.default.join(e.dest, e.keepDirStructure ? r : path__default.default.basename(r));
     return {
      src: path__default.default.join(t, r),
      dest: n,
      warn: e.warn,
      keepDirStructure: e.keepDirStructure
     };
    }(e, t, r)));
   }(e, t) : [ {
    src: getSrcAbsPath(t, e.src),
    dest: e.keepDirStructure ? path__default.default.join(e.dest, e.src) : e.dest,
    warn: e.warn,
    keepDirStructure: e.keepDirStructure
   } ];
  }(e, t)))), e = i.flat ? i.flat(1) : i.reduce(((e, t) => (e.push(...t), e)), []);
  const n = [];
  for (;e.length > 0; ) {
   const t = e.splice(0, 100);
   await Promise.all(t.map((e => processCopyTask(r, n, e))));
  }
  const o = function n(e) {
   const t = [];
   return e.forEach((e => {
    !function r(e, t) {
     (t = normalizePath(t)) !== ROOT_DIR && t + "/" !== ROOT_DIR && "" !== t && (e.includes(t) || e.push(t));
    }(t, path__default.default.dirname(e.dest));
   })), t.sort(((e, t) => {
    const r = e.split("/").length, n = t.split("/").length;
    return r < n ? -1 : r > n ? 1 : e < t ? -1 : e > t ? 1 : 0;
   })), t;
  }(n);
  try {
   await Promise.all(o.map((e => mkdir(e, {
    recursive: !0
   }))));
  } catch (e) {}
  for (;n.length > 0; ) {
   const e = n.splice(0, 100);
   await Promise.all(e.map((e => copyFile(e.src, e.dest))));
  }
 } catch (e) {
  catchError(r.diagnostics, e);
 }
 var i;
 return r;
}

function getSrcAbsPath(e, t) {
 return path__default.default.isAbsolute(t) ? t : path__default.default.join(e, t);
}

async function processCopyTask(e, t, r) {
 try {
  r.src = normalizePath(r.src), r.dest = normalizePath(r.dest), (await stat(r.src)).isDirectory() ? (e.dirPaths.includes(r.dest) || e.dirPaths.push(r.dest), 
  await async function n(e, t, r) {
   try {
    const n = await readdir(r.src);
    await Promise.all(n.map((async n => {
     const i = {
      src: path__default.default.join(r.src, n),
      dest: path__default.default.join(r.dest, n),
      warn: r.warn
     };
     await processCopyTask(e, t, i);
    })));
   } catch (t) {
    catchError(e.diagnostics, t);
   }
  }(e, t, r)) : function i(e) {
   return e = e.trim().toLowerCase(), IGNORE.some((t => e.endsWith(t)));
  }(r.src) || (e.filePaths.includes(r.dest) || e.filePaths.push(r.dest), t.push(r));
 } catch (t) {
  !1 !== r.warn && (buildError(e.diagnostics).messageText = t.message);
 }
}

function asyncGlob(e, t) {
 return new Promise(((r, n) => {
  (0, glob__default.default.glob)(e, t, ((e, t) => {
   e ? n(e) : r(t);
  }));
 }));
}

function semiver(e, t, r) {
 return e = e.split("."), t = t.split("."), fn(e[0], t[0]) || fn(e[1], t[1]) || (t[2] = t.slice(2).join("."), 
 (r = /[.-]/.test(e[2] = e.slice(2).join("."))) == /[.-]/.test(t[2]) ? fn(e[2], t[2]) : r ? -1 : 1);
}

async function checkVersion(e, t) {
 try {
  const r = await async function r(e) {
   try {
    const e = await function t() {
     return new Promise((e => {
      fs__default.default.readFile(getLastCheckStoragePath(), "utf8", ((t, r) => {
       if (!t && isString(r)) try {
        e(JSON.parse(r));
       } catch (e) {}
       e(null);
      }));
     }));
    }();
    if (null == e) return setLastCheck(), null;
    if (!function r(e, t, n) {
     return t + n < e;
    }(Date.now(), e, 6048e5)) return null;
    const t = setLastCheck(), r = await async function n(e) {
     const t = await Promise.resolve().then((function() {
      return _interopNamespace(require("https"));
     }));
     return new Promise(((r, n) => {
      const i = t.request(e, (t => {
       if (t.statusCode > 299) return void n(`url: ${e}, staus: ${t.statusCode}`);
       t.once("error", n);
       const i = [];
       t.once("end", (() => {
        r(i.join(""));
       })), t.on("data", (e => {
        i.push(e);
       }));
      }));
      i.once("error", n), i.end();
     }));
    }(REGISTRY_URL), n = JSON.parse(r);
    return await t, n["dist-tags"].latest;
   } catch (t) {
    e.debug(`getLatestCompilerVersion error: ${t}`);
   }
   return null;
  }(e);
  if (null != r) return () => {
   semiver(t, r) < 0 ? function n(e, t, r) {
    const n = "npm install @stencil/core", i = [ `Update available: ${t} ${ARROW} ${r}`, "To get the latest, please run:", n, CHANGELOG ], o = i.reduce(((e, t) => t.length > e ? t.length : e), 0), s = [];
    let a = BOX_TOP_LEFT;
    for (;a.length <= o + 2 * PADDING; ) a += BOX_HORIZONTAL;
    a += BOX_TOP_RIGHT, s.push(a), i.forEach((e => {
     let t = BOX_VERTICAL;
     for (let e = 0; e < PADDING; e++) t += " ";
     for (t += e; t.length <= o + 2 * PADDING; ) t += " ";
     t += BOX_VERTICAL, s.push(t);
    }));
    let c = BOX_BOTTOM_LEFT;
    for (;c.length <= o + 2 * PADDING; ) c += BOX_HORIZONTAL;
    c += BOX_BOTTOM_RIGHT, s.push(c);
    let l = `${INDENT}${s.join(`\n${INDENT}`)}\n`;
    l = l.replace(t, e.red(t)), l = l.replace(r, e.green(r)), l = l.replace(n, e.cyan(n)), 
    l = l.replace(CHANGELOG, e.dim(CHANGELOG)), console.log(l);
   }(e, t, r) : console.debug(`${e.cyan("@stencil/core")} version ${e.green(t)} is the latest version`);
  };
 } catch (t) {
  e.debug(`unable to load latest compiler version: ${t}`);
 }
 return noop;
}

function setLastCheck() {
 return new Promise((e => {
  const t = JSON.stringify(Date.now());
  fs__default.default.writeFile(getLastCheckStoragePath(), t, (() => {
   e();
  }));
 }));
}

function getLastCheckStoragePath() {
 return path__default.default.join(require$$6.tmpdir(), "stencil_last_version_node.json");
}

function getNextWorker(e) {
 const t = e.filter((e => !e.stopped));
 return 0 === t.length ? null : t.sort(((e, t) => e.tasks.size < t.tasks.size ? -1 : e.tasks.size > t.tasks.size ? 1 : e.totalTasksAssigned < t.totalTasksAssigned ? -1 : e.totalTasksAssigned > t.totalTasksAssigned ? 1 : 0))[0];
}

var symbols, ansiColors, create_1, fn, exit, lockfile;

Object.defineProperty(exports, "__esModule", {
 value: !0
});

const fs = require("./graceful-fs.js"), path = require("path"), require$$1 = require("util"), glob = require("./glob.js"), require$$6 = require("os"), require$$3 = require("crypto"), require$$2 = require("fs"), require$$4 = require("stream"), require$$5 = require("assert"), require$$7 = require("events"), require$$8 = require("buffer"), require$$9 = require("tty"), cp = require("child_process"), fs__default = _interopDefaultLegacy(fs), path__default = _interopDefaultLegacy(path), require$$1__default = _interopDefaultLegacy(require$$1), glob__default = _interopDefaultLegacy(glob), require$$6__default = _interopDefaultLegacy(require$$6), require$$6__namespace = _interopNamespace(require$$6), require$$3__default = _interopDefaultLegacy(require$$3), require$$2__default = _interopDefaultLegacy(require$$2), require$$4__default = _interopDefaultLegacy(require$$4), require$$5__default = _interopDefaultLegacy(require$$5), require$$7__default = _interopDefaultLegacy(require$$7), require$$8__default = _interopDefaultLegacy(require$$8), require$$9__default = _interopDefaultLegacy(require$$9), cp__namespace = _interopNamespace(cp), createTerminalLogger = e => {
 let t = "info", r = null;
 const n = [], i = e => {
  if (e.length) {
   const t = new Date, r = "[" + ("0" + t.getMinutes()).slice(-2) + ":" + ("0" + t.getSeconds()).slice(-2) + "." + Math.floor(t.getMilliseconds() / 1e3 * 10) + "]";
   e[0] = d(r) + e[0].substr(r.length);
  }
 }, o = e => {
  if (e.length) {
   const t = "[ WARN  ]";
   e[0] = h(u(t)) + e[0].substr(t.length);
  }
 }, s = e => {
  if (e.length) {
   const t = "[ ERROR ]";
   e[0] = h(l(t)) + e[0].substr(t.length);
  }
 }, a = e => {
  if (e.length) {
   const t = new Date, r = "[" + ("0" + t.getMinutes()).slice(-2) + ":" + ("0" + t.getSeconds()).slice(-2) + "." + Math.floor(t.getMilliseconds() / 1e3 * 10) + "]";
   e[0] = f(r) + e[0].substr(r.length);
  }
 }, c = (t, i) => {
  if (r) {
   const r = new Date, o = ("0" + r.getHours()).slice(-2) + ":" + ("0" + r.getMinutes()).slice(-2) + ":" + ("0" + r.getSeconds()).slice(-2) + ".0" + Math.floor(r.getMilliseconds() / 1e3 * 10) + "  " + ("000" + (e.memoryUsage() / 1e6).toFixed(1)).slice(-6) + "MB  " + t + "  " + i.join(", ");
   n.push(o);
  }
 }, l = t => e.color(t, "red"), u = t => e.color(t, "yellow"), f = t => e.color(t, "cyan"), h = t => e.color(t, "bold"), d = t => e.color(t, "dim"), p = t => e.color(t, "bgRed"), m = e => LOG_LEVELS.indexOf(e) >= LOG_LEVELS.indexOf(t), g = (t, r, n) => {
  var i, o;
  let s = t.length - r + n - 1;
  for (;t.length + INDENT$1.length > e.getColumns(); ) if (r > t.length - r + n && r > 5) t = t.substr(1), 
  r--; else {
   if (!(s > 1)) break;
   t = t.substr(0, t.length - 1), s--;
  }
  const a = [], c = Math.max(t.length, r + n);
  for (i = 0; i < c; i++) o = t.charAt(i), i >= r && i < r + n && (o = p("" === o ? " " : o)), 
  a.push(o);
  return a.join("");
 }, y = e => e.trim().startsWith("//") ? d(e) : e.split(" ").map((e => JS_KEYWORDS.indexOf(e) > -1 ? f(e) : e)).join(" "), b = e => {
  let t = !0;
  const r = [];
  for (var n = 0; n < e.length; n++) {
   const i = e.charAt(n);
   ";" === i || "{" === i ? t = !0 : ".#,:}@$[]/*".indexOf(i) > -1 && (t = !1), t && "abcdefghijklmnopqrstuvwxyz-_".indexOf(i.toLowerCase()) > -1 ? r.push(f(i)) : r.push(i);
  }
  return r.join("");
 };
 return {
  enableColors: e.enableColors,
  emoji: e.emoji,
  getLevel: () => t,
  setLevel: e => t = e,
  debug: (...t) => {
   if (m("debug")) {
    e.memoryUsage() > 0 && t.push(d(` MEM: ${(e.memoryUsage() / 1e6).toFixed(1)}MB`));
    const r = wordWrap(t, e.getColumns());
    a(r), console.log(r.join("\n"));
   }
   c("D", t);
  },
  info: (...t) => {
   if (m("info")) {
    const r = wordWrap(t, e.getColumns());
    i(r), console.log(r.join("\n"));
   }
   c("I", t);
  },
  warn: (...t) => {
   if (m("warn")) {
    const r = wordWrap(t, e.getColumns());
    o(r), console.warn("\n" + r.join("\n") + "\n");
   }
   c("W", t);
  },
  error: (...t) => {
   for (let e = 0; e < t.length; e++) if (t[e] instanceof Error) {
    const r = t[e];
    t[e] = r.message, r.stack && (t[e] += "\n" + r.stack);
   }
   if (m("error")) {
    const r = wordWrap(t, e.getColumns());
    s(r), console.error("\n" + r.join("\n") + "\n");
   }
   c("E", t);
  },
  createTimeSpan: (t, r = !1, n) => {
   const o = Date.now(), s = () => Date.now() - o, l = {
    duration: s,
    finish: (t, o, l, u) => {
     const f = s();
     let p;
     return p = f > 1e3 ? "in " + (f / 1e3).toFixed(2) + " s" : parseFloat(f.toFixed(3)) > 0 ? "in " + f + " ms" : "in less than 1 ms", 
     ((t, r, n, o, s, l, u) => {
      let f = t;
      if (n && (f = e.color(t, n)), o && (f = h(f)), f += " " + d(r), l) {
       if (m("debug")) {
        const t = [ f ], r = e.memoryUsage();
        r > 0 && t.push(d(` MEM: ${(r / 1e6).toFixed(1)}MB`));
        const n = wordWrap(t, e.getColumns());
        a(n), console.log(n.join("\n"));
       }
       c("D", [ `${t} ${r}` ]);
      } else {
       const n = wordWrap([ f ], e.getColumns());
       i(n), console.log(n.join("\n")), c("I", [ `${t} ${r}` ]), u && u.push(`${t} ${r}`);
      }
      s && console.log("");
     })(t, p, o, l, u, r, n), f;
    }
   };
   return ((t, r, n) => {
    const o = [ `${t} ${d("...")}` ];
    if (r) {
     if (m("debug")) {
      e.memoryUsage() > 0 && o.push(d(` MEM: ${(e.memoryUsage() / 1e6).toFixed(1)}MB`));
      const r = wordWrap(o, e.getColumns());
      a(r), console.log(r.join("\n")), c("D", [ `${t} ...` ]);
     }
    } else {
     const r = wordWrap(o, e.getColumns());
     i(r), console.log(r.join("\n")), c("I", [ `${t} ...` ]), n && n.push(`${t} ...`);
    }
   })(t, r, n), l;
  },
  printDiagnostics: (r, n) => {
   if (!r || 0 === r.length) return;
   let c = [ "" ];
   r.forEach((r => {
    c = c.concat(((r, n) => {
     const c = wordWrap([ r.messageText ], e.getColumns());
     let l = "";
     r.header && "Build Error" !== r.header && (l += r.header), "string" == typeof r.absFilePath && "string" != typeof r.relFilePath && ("string" != typeof n && (n = e.cwd()), 
     r.relFilePath = e.relativePath(n, r.absFilePath), r.relFilePath.includes("/") || (r.relFilePath = "./" + r.relFilePath));
     let h = r.relFilePath;
     return "string" != typeof h && (h = r.absFilePath), "string" == typeof h && (l.length > 0 && (l += ": "), 
     l += f(h), "number" == typeof r.lineNumber && r.lineNumber > -1 && (l += d(":"), 
     l += u(`${r.lineNumber}`), "number" == typeof r.columnNumber && r.columnNumber > -1 && (l += d(":"), 
     l += u(`${r.columnNumber}`)))), l.length > 0 && c.unshift(INDENT$1 + l), c.push(""), 
     r.lines && r.lines.length && (prepareLines(r.lines).forEach((e => {
      if (!isMeaningfulLine(e.text)) return;
      let t = "";
      for (e.lineNumber > -1 && (t = `L${e.lineNumber}:  `); t.length < INDENT$1.length; ) t = " " + t;
      let n = e.text;
      e.errorCharStart > -1 && (n = g(n, e.errorCharStart, e.errorLength)), t = d(t), 
      "typescript" === r.language || "javascript" === r.language ? t += y(n) : "scss" === r.language || "css" === r.language ? t += b(n) : t += n, 
      c.push(t);
     })), c.push("")), "error" === r.level ? s(c) : "warn" === r.level ? o(c) : "debug" === r.level ? a(c) : i(c), 
     null != r.debugText && "debug" === t && (c.push(r.debugText), a(wordWrap([ r.debugText ], e.getColumns()))), 
     c;
    })(r, n));
   })), console.log(c.join("\n"));
  },
  red: l,
  green: t => e.color(t, "green"),
  yellow: u,
  blue: t => e.color(t, "blue"),
  magenta: t => e.color(t, "magenta"),
  cyan: f,
  gray: t => e.color(t, "gray"),
  bold: h,
  dim: d,
  bgRed: p,
  setLogFilePath: e => r = e,
  writeLogs: t => {
   if (r) try {
    c("F", [ "--------------------------------------" ]), e.writeLogs(r, n.join("\n"), t);
   } catch (e) {}
   n.length = 0;
  }
 };
}, LOG_LEVELS = [ "debug", "info", "warn", "error" ], wordWrap = (e, t) => {
 const r = [], n = [];
 e.forEach((e => {
  null === e ? n.push("null") : void 0 === e ? n.push("undefined") : "string" == typeof e ? e.replace(/\s/gm, " ").split(" ").forEach((e => {
   e.trim().length && n.push(e.trim());
  })) : "number" == typeof e || "boolean" == typeof e || "function" == typeof e ? n.push(e.toString()) : Array.isArray(e) || Object(e) === e ? n.push((() => e.toString())) : n.push(e.toString());
 }));
 let i = INDENT$1;
 return n.forEach((e => {
  r.length > 25 || ("function" == typeof e ? (i.trim().length && r.push(i), r.push(e()), 
  i = INDENT$1) : INDENT$1.length + e.length > t - 1 ? (i.trim().length && r.push(i), 
  r.push(INDENT$1 + e), i = INDENT$1) : e.length + i.length > t - 1 ? (r.push(i), 
  i = INDENT$1 + e + " ") : i += e + " ");
 })), i.trim().length && r.push(i), r.map((e => e.trimRight()));
}, prepareLines = e => {
 const t = JSON.parse(JSON.stringify(e));
 for (let e = 0; e < 100; e++) {
  if (!eachLineHasLeadingWhitespace(t)) return t;
  for (let e = 0; e < t.length; e++) if (t[e].text = t[e].text.substr(1), t[e].errorCharStart--, 
  !t[e].text.length) return t;
 }
 return t;
}, eachLineHasLeadingWhitespace = e => {
 if (!e.length) return !1;
 for (var t = 0; t < e.length; t++) {
  if (!e[t].text || e[t].text.length < 1) return !1;
  const r = e[t].text.charAt(0);
  if (" " !== r && "\t" !== r) return !1;
 }
 return !0;
}, isMeaningfulLine = e => !!e && (e = e.trim()).length > 0, JS_KEYWORDS = [ "abstract", "any", "as", "break", "boolean", "case", "catch", "class", "console", "const", "continue", "debugger", "declare", "default", "delete", "do", "else", "enum", "export", "extends", "false", "finally", "for", "from", "function", "get", "if", "import", "in", "implements", "Infinity", "instanceof", "let", "module", "namespace", "NaN", "new", "number", "null", "public", "private", "protected", "require", "return", "static", "set", "string", "super", "switch", "this", "throw", "try", "true", "type", "typeof", "undefined", "var", "void", "with", "while", "yield" ], INDENT$1 = "           ";

symbols = createCommonjsModule((function(e) {
 const t = "Hyper" === process.env.TERM_PROGRAM, r = "win32" === process.platform, n = "linux" === process.platform, i = {
  ballotDisabled: "☒",
  ballotOff: "☐",
  ballotOn: "☑",
  bullet: "•",
  bulletWhite: "◦",
  fullBlock: "█",
  heart: "❤",
  identicalTo: "≡",
  line: "─",
  mark: "※",
  middot: "·",
  minus: "－",
  multiplication: "×",
  obelus: "÷",
  pencilDownRight: "✎",
  pencilRight: "✏",
  pencilUpRight: "✐",
  percent: "%",
  pilcrow2: "❡",
  pilcrow: "¶",
  plusMinus: "±",
  section: "§",
  starsOff: "☆",
  starsOn: "★",
  upDownArrow: "↕"
 }, o = Object.assign({}, i, {
  check: "√",
  cross: "×",
  ellipsisLarge: "...",
  ellipsis: "...",
  info: "i",
  question: "?",
  questionSmall: "?",
  pointer: ">",
  pointerSmall: "»",
  radioOff: "( )",
  radioOn: "(*)",
  warning: "‼"
 }), s = Object.assign({}, i, {
  ballotCross: "✘",
  check: "✔",
  cross: "✖",
  ellipsisLarge: "⋯",
  ellipsis: "…",
  info: "ℹ",
  question: "?",
  questionFull: "？",
  questionSmall: "﹖",
  pointer: n ? "▸" : "❯",
  pointerSmall: n ? "‣" : "›",
  radioOff: "◯",
  radioOn: "◉",
  warning: "⚠"
 });
 e.exports = r && !t ? o : s, Reflect.defineProperty(e.exports, "common", {
  enumerable: !1,
  value: i
 }), Reflect.defineProperty(e.exports, "windows", {
  enumerable: !1,
  value: o
 }), Reflect.defineProperty(e.exports, "other", {
  enumerable: !1,
  value: s
 });
}));

const ANSI_REGEX = /[\u001b\u009b][[\]#;?()]*(?:(?:(?:[^\W_]*;?[^\W_]*)\u0007)|(?:(?:[0-9]{1,4}(;[0-9]{0,4})*)?[~0-9=<>cf-nqrtyA-PRZ]))/g, create = () => {
 const e = {
  enabled: !0,
  visible: !0,
  styles: {},
  keys: {}
 };
 "FORCE_COLOR" in process.env && (e.enabled = "0" !== process.env.FORCE_COLOR);
 const t = (e, t, r) => "function" == typeof e ? e(t) : e.wrap(t, r), r = (r, n) => {
  if ("" === r || null == r) return "";
  if (!1 === e.enabled) return r;
  if (!1 === e.visible) return "";
  let i = "" + r, o = i.includes("\n"), s = n.length;
  for (s > 0 && n.includes("unstyle") && (n = [ ...new Set([ "unstyle", ...n ]) ].reverse()); s-- > 0; ) i = t(e.styles[n[s]], i, o);
  return i;
 }, n = (t, n, i) => {
  e.styles[t] = (e => {
   let t = e.open = `[${e.codes[0]}m`, r = e.close = `[${e.codes[1]}m`, n = e.regex = new RegExp(`\\u001b\\[${e.codes[1]}m`, "g");
   return e.wrap = (e, i) => {
    e.includes(r) && (e = e.replace(n, r + t));
    let o = t + e + r;
    return i ? o.replace(/\r*\n/g, `${r}$&${t}`) : o;
   }, e;
  })({
   name: t,
   codes: n
  }), (e.keys[i] || (e.keys[i] = [])).push(t), Reflect.defineProperty(e, t, {
   configurable: !0,
   enumerable: !0,
   set(r) {
    e.alias(t, r);
   },
   get() {
    let n = e => r(e, n.stack);
    return Reflect.setPrototypeOf(n, e), n.stack = this.stack ? this.stack.concat(t) : [ t ], 
    n;
   }
  });
 };
 return n("reset", [ 0, 0 ], "modifier"), n("bold", [ 1, 22 ], "modifier"), n("dim", [ 2, 22 ], "modifier"), 
 n("italic", [ 3, 23 ], "modifier"), n("underline", [ 4, 24 ], "modifier"), n("inverse", [ 7, 27 ], "modifier"), 
 n("hidden", [ 8, 28 ], "modifier"), n("strikethrough", [ 9, 29 ], "modifier"), n("black", [ 30, 39 ], "color"), 
 n("red", [ 31, 39 ], "color"), n("green", [ 32, 39 ], "color"), n("yellow", [ 33, 39 ], "color"), 
 n("blue", [ 34, 39 ], "color"), n("magenta", [ 35, 39 ], "color"), n("cyan", [ 36, 39 ], "color"), 
 n("white", [ 37, 39 ], "color"), n("gray", [ 90, 39 ], "color"), n("grey", [ 90, 39 ], "color"), 
 n("bgBlack", [ 40, 49 ], "bg"), n("bgRed", [ 41, 49 ], "bg"), n("bgGreen", [ 42, 49 ], "bg"), 
 n("bgYellow", [ 43, 49 ], "bg"), n("bgBlue", [ 44, 49 ], "bg"), n("bgMagenta", [ 45, 49 ], "bg"), 
 n("bgCyan", [ 46, 49 ], "bg"), n("bgWhite", [ 47, 49 ], "bg"), n("blackBright", [ 90, 39 ], "bright"), 
 n("redBright", [ 91, 39 ], "bright"), n("greenBright", [ 92, 39 ], "bright"), n("yellowBright", [ 93, 39 ], "bright"), 
 n("blueBright", [ 94, 39 ], "bright"), n("magentaBright", [ 95, 39 ], "bright"), 
 n("cyanBright", [ 96, 39 ], "bright"), n("whiteBright", [ 97, 39 ], "bright"), n("bgBlackBright", [ 100, 49 ], "bgBright"), 
 n("bgRedBright", [ 101, 49 ], "bgBright"), n("bgGreenBright", [ 102, 49 ], "bgBright"), 
 n("bgYellowBright", [ 103, 49 ], "bgBright"), n("bgBlueBright", [ 104, 49 ], "bgBright"), 
 n("bgMagentaBright", [ 105, 49 ], "bgBright"), n("bgCyanBright", [ 106, 49 ], "bgBright"), 
 n("bgWhiteBright", [ 107, 49 ], "bgBright"), e.ansiRegex = ANSI_REGEX, e.hasColor = e.hasAnsi = t => (e.ansiRegex.lastIndex = 0, 
 "string" == typeof t && "" !== t && e.ansiRegex.test(t)), e.alias = (t, n) => {
  let i = "string" == typeof n ? e[n] : n;
  if ("function" != typeof i) throw new TypeError("Expected alias to be the name of an existing color (string) or a function");
  i.stack || (Reflect.defineProperty(i, "name", {
   value: t
  }), e.styles[t] = i, i.stack = [ t ]), Reflect.defineProperty(e, t, {
   configurable: !0,
   enumerable: !0,
   set(r) {
    e.alias(t, r);
   },
   get() {
    let t = e => r(e, t.stack);
    return Reflect.setPrototypeOf(t, e), t.stack = this.stack ? this.stack.concat(i.stack) : i.stack, 
    t;
   }
  });
 }, e.theme = t => {
  if (null === (r = t) || "object" != typeof r || Array.isArray(r)) throw new TypeError("Expected theme to be an object");
  var r;
  for (let r of Object.keys(t)) e.alias(r, t[r]);
  return e;
 }, e.alias("unstyle", (t => "string" == typeof t && "" !== t ? (e.ansiRegex.lastIndex = 0, 
 t.replace(e.ansiRegex, "")) : "")), e.alias("noop", (e => e)), e.none = e.clear = e.noop, 
 e.stripColor = e.unstyle, e.symbols = symbols, e.define = n, e;
};

ansiColors = create(), create_1 = create, ansiColors.create = create_1;

const noop = () => {}, isString = e => "string" == typeof e, buildError = e => {
 const t = {
  level: "error",
  type: "build",
  header: "Build Error",
  messageText: "build error",
  relFilePath: null,
  absFilePath: null,
  lines: []
 };
 return e && e.push(t), t;
}, catchError = (e, t, r) => {
 const n = {
  level: "error",
  type: "build",
  header: "Build Error",
  messageText: "build error",
  relFilePath: null,
  absFilePath: null,
  lines: []
 };
 return isString(r) ? n.messageText = r : null != t && (null != t.stack ? n.messageText = t.stack.toString() : null != t.message ? n.messageText = t.message.toString() : n.messageText = t.toString()), 
 null == e || shouldIgnoreError(n.messageText) || e.push(n), n;
}, shouldIgnoreError = e => e === TASK_CANCELED_MSG, TASK_CANCELED_MSG = "task canceled", normalizePath = e => {
 if ("string" != typeof e) throw new Error("invalid path to normalize");
 e = normalizeSlashes(e.trim());
 const t = pathComponents(e, getRootLength(e)), r = reducePathComponents(t), n = r[0], i = r[1], o = n + r.slice(1).join("/");
 return "" === o ? "." : "" === n && i && e.includes("/") && !i.startsWith(".") && !i.startsWith("@") ? "./" + o : o;
}, normalizeSlashes = e => e.replace(backslashRegExp, "/"), backslashRegExp = /\\/g, reducePathComponents = e => {
 if (!Array.isArray(e) || 0 === e.length) return [];
 const t = [ e[0] ];
 for (let r = 1; r < e.length; r++) {
  const n = e[r];
  if (n && "." !== n) {
   if (".." === n) if (t.length > 1) {
    if (".." !== t[t.length - 1]) {
     t.pop();
     continue;
    }
   } else if (t[0]) continue;
   t.push(n);
  }
 }
 return t;
}, getRootLength = e => {
 const t = getEncodedRootLength(e);
 return t < 0 ? ~t : t;
}, getEncodedRootLength = e => {
 if (!e) return 0;
 const t = e.charCodeAt(0);
 if (47 === t || 92 === t) {
  if (e.charCodeAt(1) !== t) return 1;
  const r = e.indexOf(47 === t ? "/" : "\\", 2);
  return r < 0 ? e.length : r + 1;
 }
 if (isVolumeCharacter(t) && 58 === e.charCodeAt(1)) {
  const t = e.charCodeAt(2);
  if (47 === t || 92 === t) return 3;
  if (2 === e.length) return 2;
 }
 const r = e.indexOf("://");
 if (-1 !== r) {
  const t = r + "://".length, n = e.indexOf("/", t);
  if (-1 !== n) {
   const i = e.slice(0, r), o = e.slice(t, n);
   if ("file" === i && ("" === o || "localhost" === o) && isVolumeCharacter(e.charCodeAt(n + 1))) {
    const t = getFileUrlVolumeSeparatorEnd(e, n + 2);
    if (-1 !== t) {
     if (47 === e.charCodeAt(t)) return ~(t + 1);
     if (t === e.length) return ~t;
    }
   }
   return ~(n + 1);
  }
  return ~e.length;
 }
 return 0;
}, isVolumeCharacter = e => e >= 97 && e <= 122 || e >= 65 && e <= 90, getFileUrlVolumeSeparatorEnd = (e, t) => {
 const r = e.charCodeAt(t);
 if (58 === r) return t + 1;
 if (37 === r && 51 === e.charCodeAt(t + 1)) {
  const r = e.charCodeAt(t + 2);
  if (97 === r || 65 === r) return t + 3;
 }
 return -1;
}, pathComponents = (e, t) => {
 const r = e.substring(0, t), n = e.substring(t).split("/"), i = n.length;
 return i > 0 && !n[i - 1] && n.pop(), [ r, ...n ];
}, copyFile = require$$1.promisify(fs__default.default.copyFile), mkdir = require$$1.promisify(fs__default.default.mkdir), readdir = require$$1.promisify(fs__default.default.readdir);

require$$1.promisify(fs__default.default.readFile);

const stat = require$$1.promisify(fs__default.default.stat), ROOT_DIR = normalizePath(path__default.default.resolve("/")), IGNORE = [ ".ds_store", ".gitignore", "desktop.ini", "thumbs.db" ];

fn = new Intl.Collator(0, {
 numeric: 1
}).compare;

const REGISTRY_URL = "https://registry.npmjs.org/@stencil/core", CHANGELOG = "https://github.com/ionic-team/stencil/blob/main/CHANGELOG.md", ARROW = "→", BOX_TOP_LEFT = "╭", BOX_TOP_RIGHT = "╮", BOX_BOTTOM_LEFT = "╰", BOX_BOTTOM_RIGHT = "╯", BOX_VERTICAL = "│", BOX_HORIZONTAL = "─", PADDING = 2, INDENT = "   ";

exit = function e(t, r) {
 function n() {
  i === r.length && process.exit(t);
 }
 r || (r = [ process.stdout, process.stderr ]);
 var i = 0;
 r.forEach((function(e) {
  0 === e.bufferSize ? i++ : e.write("", "utf-8", (function() {
   i++, n();
  })), e.write = function() {};
 })), n(), process.on("exit", (function() {
  process.exit(t);
 }));
}, lockfile = createCommonjsModule((function(e) {
 e.exports = function(e) {
  function t(n) {
   if (r[n]) return r[n].exports;
   var i = r[n] = {
    i: n,
    l: !1,
    exports: {}
   };
   return e[n].call(i.exports, i, i.exports, t), i.l = !0, i.exports;
  }
  var r = {};
  return t.m = e, t.c = r, t.i = function(e) {
   return e;
  }, t.d = function(e, r, n) {
   t.o(e, r) || Object.defineProperty(e, r, {
    configurable: !1,
    enumerable: !0,
    get: n
   });
  }, t.n = function(e) {
   var r = e && e.__esModule ? function t() {
    return e.default;
   } : function t() {
    return e;
   };
   return t.d(r, "a", r), r;
  }, t.o = function(e, t) {
   return Object.prototype.hasOwnProperty.call(e, t);
  }, t.p = "", t(t.s = 14);
 }([ function(e, t) {
  e.exports = path__default.default;
 }, function(e, t, r) {
  var n, i;
  t.__esModule = !0, n = r(173), i = function o(e) {
   return e && e.__esModule ? e : {
    default: e
   };
  }(n), t.default = function(e) {
   return function() {
    var t = e.apply(this, arguments);
    return new i.default((function(e, r) {
     return function n(o, s) {
      var a, c;
      try {
       c = (a = t[o](s)).value;
      } catch (e) {
       return void r(e);
      }
      if (!a.done) return i.default.resolve(c).then((function(e) {
       n("next", e);
      }), (function(e) {
       n("throw", e);
      }));
      e(c);
     }("next");
    }));
   };
  };
 }, function(e, t) {
  e.exports = require$$1__default.default;
 }, function(e, t) {
  e.exports = require$$2__default.default;
 }, function(e, t, r) {
  Object.defineProperty(t, "__esModule", {
   value: !0
  });
  class n extends Error {
   constructor(e, t) {
    super(e), this.code = t;
   }
  }
  t.MessageError = n, t.ProcessSpawnError = class i extends n {
   constructor(e, t, r) {
    super(e, t), this.process = r;
   }
  }, t.SecurityError = class o extends n {}, t.ProcessTermError = class s extends n {};
  class a extends Error {
   constructor(e, t) {
    super(e), this.responseCode = t;
   }
  }
  t.ResponseError = a;
 }, function(e, t, r) {
  function n() {
   return p = u(r(1));
  }
  function i() {
   return m = u(r(3));
  }
  function o() {
   return y = u(r(36));
  }
  function s() {
   return b = u(r(0));
  }
  function a() {
   return _ = function e(t) {
    var r, n;
    if (t && t.__esModule) return t;
    if (r = {}, null != t) for (n in t) Object.prototype.hasOwnProperty.call(t, n) && (r[n] = t[n]);
    return r.default = t, r;
   }(r(40));
  }
  function c() {
   return w = r(40);
  }
  function l() {
   return E = r(164);
  }
  function u(e) {
   return e && e.__esModule ? e : {
    default: e
   };
  }
  function f(e, t) {
   return new Promise(((r, n) => {
    (m || i()).default.readFile(e, t, (function(e, t) {
     e ? n(e) : r(t);
    }));
   }));
  }
  function h(e) {
   return f(e, "utf8").then(d);
  }
  function d(e) {
   return e.replace(/\r\n/g, "\n");
  }
  var p, m, g, y, b, v, _, w, k, E, x, O, S, C, A, j, T, P, F, L, $, N, D, R, M, I, q;
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.getFirstSuitableFolder = t.readFirstAvailableStream = t.makeTempDir = t.hardlinksWork = t.writeFilePreservingEol = t.getFileSizeOnDisk = t.walk = t.symlink = t.find = t.readJsonAndFile = t.readJson = t.readFileAny = t.hardlinkBulk = t.copyBulk = t.unlink = t.glob = t.link = t.chmod = t.lstat = t.exists = t.mkdirp = t.stat = t.access = t.rename = t.readdir = t.realpath = t.readlink = t.writeFile = t.open = t.readFileBuffer = t.lockQueue = t.constants = void 0;
  let G = (x = (0, (p || n()).default)((function*(e, t, r, i) {
   var o, a, c, u, f, h, d, m, g, y, v, _, w;
   let k = (w = (0, (p || n()).default)((function*(n) {
    var o, a, c, u, f, h, d, p, m, g, y, v;
    const _ = n.src, w = n.dest, k = n.type, C = n.onFresh || pe, A = n.onDone || pe;
    if (O.has(w.toLowerCase()) ? i.verbose(`The case-insensitive file ${w} shouldn't be copied twice in one bulk copy`) : O.add(w.toLowerCase()), 
    "symlink" === k) return yield oe((b || s()).default.dirname(w)), C(), S.symlink.push({
     dest: w,
     linkname: _
    }), void A();
    if (t.ignoreBasenames.indexOf((b || s()).default.basename(_)) >= 0) return;
    const j = yield ae(_);
    let T, P;
    j.isDirectory() && (T = yield ne(_));
    try {
     P = yield ae(w);
    } catch (e) {
     if ("ENOENT" !== e.code) throw e;
    }
    if (P) {
     const e = j.isSymbolicLink() && P.isSymbolicLink(), t = j.isDirectory() && P.isDirectory(), n = j.isFile() && P.isFile();
     if (n && x.has(w)) return A(), void i.verbose(i.lang("verboseFileSkipArtifact", _));
     if (n && j.size === P.size && (0, (E || l()).fileDatesEqual)(j.mtime, P.mtime)) return A(), 
     void i.verbose(i.lang("verboseFileSkip", _, w, j.size, +j.mtime));
     if (e) {
      const e = yield te(_);
      if (e === (yield te(w))) return A(), void i.verbose(i.lang("verboseFileSkipSymlink", _, w, e));
     }
     if (t) {
      const e = yield ne(w);
      for (he(T, "src files not initialised"), o = e, c = 0, o = (a = Array.isArray(o)) ? o : o[Symbol.iterator](); ;) {
       if (a) {
        if (c >= o.length) break;
        u = o[c++];
       } else {
        if ((c = o.next()).done) break;
        u = c.value;
       }
       const e = u;
       if (T.indexOf(e) < 0) {
        const t = (b || s()).default.join(w, e);
        if (r.add(t), (yield ae(t)).isDirectory()) for (f = yield ne(t), d = 0, f = (h = Array.isArray(f)) ? f : f[Symbol.iterator](); ;) {
         if (h) {
          if (d >= f.length) break;
          p = f[d++];
         } else {
          if ((d = f.next()).done) break;
          p = d.value;
         }
         const e = p;
         r.add((b || s()).default.join(t, e));
        }
       }
      }
     }
    }
    if (P && P.isSymbolicLink() && (yield (0, (E || l()).unlink)(w), P = null), j.isSymbolicLink()) {
     C();
     const e = yield te(_);
     S.symlink.push({
      dest: w,
      linkname: e
     }), A();
    } else if (j.isDirectory()) {
     P || (i.verbose(i.lang("verboseFileFolder", w)), yield oe(w));
     const t = w.split((b || s()).default.sep);
     for (;t.length; ) O.add(t.join((b || s()).default.sep).toLowerCase()), t.pop();
     he(T, "src files not initialised");
     let r = T.length;
     for (r || A(), m = T, y = 0, m = (g = Array.isArray(m)) ? m : m[Symbol.iterator](); ;) {
      if (g) {
       if (y >= m.length) break;
       v = m[y++];
      } else {
       if ((y = m.next()).done) break;
       v = y.value;
      }
      const t = v;
      e.push({
       dest: (b || s()).default.join(w, t),
       onFresh: C,
       onDone: function(e) {
        function t() {
         return e.apply(this, arguments);
        }
        return t.toString = function() {
         return e.toString();
        }, t;
       }((function() {
        0 == --r && A();
       })),
       src: (b || s()).default.join(_, t)
      });
     }
    } else {
     if (!j.isFile()) throw new Error(`unsure how to copy this: ${_}`);
     C(), S.file.push({
      src: _,
      dest: w,
      atime: j.atime,
      mtime: j.mtime,
      mode: j.mode
     }), A();
    }
   })), function e(t) {
    return w.apply(this, arguments);
   });
   const x = new Set(t.artifactFiles || []), O = new Set;
   for (o = e, c = 0, o = (a = Array.isArray(o)) ? o : o[Symbol.iterator](); ;) {
    if (a) {
     if (c >= o.length) break;
     u = o[c++];
    } else {
     if ((c = o.next()).done) break;
     u = c.value;
    }
    const e = u, r = e.onDone;
    e.onDone = function() {
     t.onProgress(e.dest), r && r();
    };
   }
   t.onStart(e.length);
   const S = {
    file: [],
    symlink: [],
    link: []
   };
   for (;e.length; ) {
    const t = e.splice(0, ue);
    yield Promise.all(t.map(k));
   }
   for (f = x, d = 0, f = (h = Array.isArray(f)) ? f : f[Symbol.iterator](); ;) {
    if (h) {
     if (d >= f.length) break;
     m = f[d++];
    } else {
     if ((d = f.next()).done) break;
     m = d.value;
    }
    const e = m;
    r.has(e) && (i.verbose(i.lang("verboseFilePhantomExtraneous", e)), r.delete(e));
   }
   for (g = r, v = 0, g = (y = Array.isArray(g)) ? g : g[Symbol.iterator](); ;) {
    if (y) {
     if (v >= g.length) break;
     _ = g[v++];
    } else {
     if ((v = g.next()).done) break;
     _ = v.value;
    }
    const e = _;
    O.has(e.toLowerCase()) && r.delete(e);
   }
   return S;
  })), function e(t, r, n, i) {
   return x.apply(this, arguments);
  }), B = (O = (0, (p || n()).default)((function*(e, t, r, i) {
   var o, a, c, l, u, f, h, d, m, g, y, v, _;
   let w = (_ = (0, (p || n()).default)((function*(n) {
    var o, a, c, l, u, f, h, d, p, m, g, y;
    const v = n.src, _ = n.dest, w = n.onFresh || pe, O = n.onDone || pe;
    if (E.has(_.toLowerCase())) return void O();
    if (E.add(_.toLowerCase()), t.ignoreBasenames.indexOf((b || s()).default.basename(v)) >= 0) return;
    const S = yield ae(v);
    let C;
    S.isDirectory() && (C = yield ne(v));
    const A = yield se(_);
    if (A) {
     const e = yield ae(_), t = S.isSymbolicLink() && e.isSymbolicLink(), n = S.isDirectory() && e.isDirectory(), p = S.isFile() && e.isFile();
     if (S.mode !== e.mode) try {
      yield ie(_, S.mode);
     } catch (e) {
      i.verbose(e);
     }
     if (p && k.has(_)) return O(), void i.verbose(i.lang("verboseFileSkipArtifact", v));
     if (p && null !== S.ino && S.ino === e.ino) return O(), void i.verbose(i.lang("verboseFileSkip", v, _, S.ino));
     if (t) {
      const e = yield te(v);
      if (e === (yield te(_))) return O(), void i.verbose(i.lang("verboseFileSkipSymlink", v, _, e));
     }
     if (n) {
      const e = yield ne(_);
      for (he(C, "src files not initialised"), o = e, c = 0, o = (a = Array.isArray(o)) ? o : o[Symbol.iterator](); ;) {
       if (a) {
        if (c >= o.length) break;
        l = o[c++];
       } else {
        if ((c = o.next()).done) break;
        l = c.value;
       }
       const e = l;
       if (C.indexOf(e) < 0) {
        const t = (b || s()).default.join(_, e);
        if (r.add(t), (yield ae(t)).isDirectory()) for (u = yield ne(t), h = 0, u = (f = Array.isArray(u)) ? u : u[Symbol.iterator](); ;) {
         if (f) {
          if (h >= u.length) break;
          d = u[h++];
         } else {
          if ((h = u.next()).done) break;
          d = h.value;
         }
         const e = d;
         r.add((b || s()).default.join(t, e));
        }
       }
      }
     }
    }
    if (S.isSymbolicLink()) {
     w();
     const e = yield te(v);
     x.symlink.push({
      dest: _,
      linkname: e
     }), O();
    } else if (S.isDirectory()) {
     i.verbose(i.lang("verboseFileFolder", _)), yield oe(_);
     const t = _.split((b || s()).default.sep);
     for (;t.length; ) E.add(t.join((b || s()).default.sep).toLowerCase()), t.pop();
     he(C, "src files not initialised");
     let r = C.length;
     for (r || O(), p = C, g = 0, p = (m = Array.isArray(p)) ? p : p[Symbol.iterator](); ;) {
      if (m) {
       if (g >= p.length) break;
       y = p[g++];
      } else {
       if ((g = p.next()).done) break;
       y = g.value;
      }
      const t = y;
      e.push({
       onFresh: w,
       src: (b || s()).default.join(v, t),
       dest: (b || s()).default.join(_, t),
       onDone: function(e) {
        function t() {
         return e.apply(this, arguments);
        }
        return t.toString = function() {
         return e.toString();
        }, t;
       }((function() {
        0 == --r && O();
       }))
      });
     }
    } else {
     if (!S.isFile()) throw new Error(`unsure how to copy this: ${v}`);
     w(), x.link.push({
      src: v,
      dest: _,
      removeDest: A
     }), O();
    }
   })), function e(t) {
    return _.apply(this, arguments);
   });
   const k = new Set(t.artifactFiles || []), E = new Set;
   for (o = e, c = 0, o = (a = Array.isArray(o)) ? o : o[Symbol.iterator](); ;) {
    if (a) {
     if (c >= o.length) break;
     l = o[c++];
    } else {
     if ((c = o.next()).done) break;
     l = c.value;
    }
    const e = l, r = e.onDone || pe;
    e.onDone = function() {
     t.onProgress(e.dest), r();
    };
   }
   t.onStart(e.length);
   const x = {
    file: [],
    symlink: [],
    link: []
   };
   for (;e.length; ) {
    const t = e.splice(0, ue);
    yield Promise.all(t.map(w));
   }
   for (u = k, h = 0, u = (f = Array.isArray(u)) ? u : u[Symbol.iterator](); ;) {
    if (f) {
     if (h >= u.length) break;
     d = u[h++];
    } else {
     if ((h = u.next()).done) break;
     d = h.value;
    }
    const e = d;
    r.has(e) && (i.verbose(i.lang("verboseFilePhantomExtraneous", e)), r.delete(e));
   }
   for (m = r, y = 0, m = (g = Array.isArray(m)) ? m : m[Symbol.iterator](); ;) {
    if (g) {
     if (y >= m.length) break;
     v = m[y++];
    } else {
     if ((y = m.next()).done) break;
     v = y.value;
    }
    const e = v;
    E.has(e.toLowerCase()) && r.delete(e);
   }
   return x;
  })), function e(t, r, n, i) {
   return O.apply(this, arguments);
  }), z = t.copyBulk = (S = (0, (p || n()).default)((function*(e, t, r) {
   const i = {
    onStart: r && r.onStart || pe,
    onProgress: r && r.onProgress || pe,
    possibleExtraneous: r ? r.possibleExtraneous : new Set,
    ignoreBasenames: r && r.ignoreBasenames || [],
    artifactFiles: r && r.artifactFiles || []
   }, o = yield G(e, i, i.possibleExtraneous, t);
   i.onStart(o.file.length + o.symlink.length + o.link.length);
   const c = o.file, u = new Map;
   var f;
   yield (_ || a()).queue(c, (f = (0, (p || n()).default)((function*(e) {
    let r;
    for (;r = u.get(e.dest); ) yield r;
    t.verbose(t.lang("verboseFileCopy", e.src, e.dest));
    const n = (0, (E || l()).copyFile)(e, (function() {
     return u.delete(e.dest);
    }));
    return u.set(e.dest, n), i.onProgress(e.dest), n;
   })), function(e) {
    return f.apply(this, arguments);
   }), ue);
   const h = o.symlink;
   yield (_ || a()).queue(h, (function(e) {
    const r = (b || s()).default.resolve((b || s()).default.dirname(e.dest), e.linkname);
    return t.verbose(t.lang("verboseFileSymlink", e.dest, r)), U(r, e.dest);
   }));
  })), function e(t, r, n) {
   return S.apply(this, arguments);
  });
  t.hardlinkBulk = (C = (0, (p || n()).default)((function*(e, t, r) {
   const i = {
    onStart: r && r.onStart || pe,
    onProgress: r && r.onProgress || pe,
    possibleExtraneous: r ? r.possibleExtraneous : new Set,
    artifactFiles: r && r.artifactFiles || [],
    ignoreBasenames: []
   }, o = yield B(e, i, i.possibleExtraneous, t);
   i.onStart(o.file.length + o.symlink.length + o.link.length);
   const c = o.link;
   var u;
   yield (_ || a()).queue(c, (u = (0, (p || n()).default)((function*(e) {
    t.verbose(t.lang("verboseFileLink", e.src, e.dest)), e.removeDest && (yield (0, 
    (E || l()).unlink)(e.dest)), yield ce(e.src, e.dest);
   })), function(e) {
    return u.apply(this, arguments);
   }), ue);
   const f = o.symlink;
   yield (_ || a()).queue(f, (function(e) {
    const r = (b || s()).default.resolve((b || s()).default.dirname(e.dest), e.linkname);
    return t.verbose(t.lang("verboseFileSymlink", e.dest, r)), U(r, e.dest);
   }));
  })), function e(t, r, n) {
   return C.apply(this, arguments);
  }), t.readFileAny = (A = (0, (p || n()).default)((function*(e) {
   var t, r, n, i;
   for (t = e, n = 0, t = (r = Array.isArray(t)) ? t : t[Symbol.iterator](); ;) {
    if (r) {
     if (n >= t.length) break;
     i = t[n++];
    } else {
     if ((n = t.next()).done) break;
     i = n.value;
    }
    const e = i;
    if (yield se(e)) return h(e);
   }
   return null;
  })), function e(t) {
   return A.apply(this, arguments);
  }), t.readJson = (j = (0, (p || n()).default)((function*(e) {
   return (yield W(e)).object;
  })), function e(t) {
   return j.apply(this, arguments);
  });
  let W = t.readJsonAndFile = (T = (0, (p || n()).default)((function*(e) {
   const t = yield h(e);
   try {
    return {
     object: (0, (k || (k = u(r(20)))).default)(JSON.parse(de(t))),
     content: t
    };
   } catch (t) {
    throw t.message = `${e}: ${t.message}`, t;
   }
  })), function e(t) {
   return T.apply(this, arguments);
  });
  t.find = (P = (0, (p || n()).default)((function*(e, t) {
   const r = t.split((b || s()).default.sep);
   for (;r.length; ) {
    const t = r.concat(e).join((b || s()).default.sep);
    if (yield se(t)) return t;
    r.pop();
   }
   return !1;
  })), function e(t, r) {
   return P.apply(this, arguments);
  });
  let U = t.symlink = (F = (0, (p || n()).default)((function*(e, t) {
   try {
    if ((yield ae(t)).isSymbolicLink() && (yield re(t)) === e) return;
   } catch (e) {
    if ("ENOENT" !== e.code) throw e;
   }
   if (yield (0, (E || l()).unlink)(t), "win32" === process.platform) yield fe(e, t, "junction"); else {
    let r;
    try {
     r = (b || s()).default.relative((m || i()).default.realpathSync((b || s()).default.dirname(t)), (m || i()).default.realpathSync(e));
    } catch (n) {
     if ("ENOENT" !== n.code) throw n;
     r = (b || s()).default.relative((b || s()).default.dirname(t), e);
    }
    yield fe(r || ".", t);
   }
  })), function e(t, r) {
   return F.apply(this, arguments);
  }), H = t.walk = (L = (0, (p || n()).default)((function*(e, t, r = new Set) {
   var n, i, o, a;
   let c = [], l = yield ne(e);
   for (r.size && (l = l.filter((function(e) {
    return !r.has(e);
   }))), n = l, o = 0, n = (i = Array.isArray(n)) ? n : n[Symbol.iterator](); ;) {
    if (i) {
     if (o >= n.length) break;
     a = n[o++];
    } else {
     if ((o = n.next()).done) break;
     a = o.value;
    }
    const l = a, u = t ? (b || s()).default.join(t, l) : l, f = (b || s()).default.join(e, l), h = yield ae(f);
    c.push({
     relative: u,
     basename: l,
     absolute: f,
     mtime: +h.mtime
    }), h.isDirectory() && (c = c.concat(yield H(f, u, r)));
   }
   return c;
  })), function e(t, r) {
   return L.apply(this, arguments);
  });
  t.getFileSizeOnDisk = ($ = (0, (p || n()).default)((function*(e) {
   const t = yield ae(e), r = t.size, n = t.blksize;
   return Math.ceil(r / n) * n;
  })), function e(t) {
   return $.apply(this, arguments);
  });
  let K = (N = (0, (p || n()).default)((function*(e) {
   if (!(yield se(e))) return;
   const t = yield J(e);
   for (let e = 0; e < t.length; ++e) {
    if (t[e] === me) return "\r\n";
    if (t[e] === ge) return "\n";
   }
  })), function e(t) {
   return N.apply(this, arguments);
  });
  t.writeFilePreservingEol = (D = (0, (p || n()).default)((function*(e, t) {
   const r = (yield K(e)) || (y || o()).default.EOL;
   "\n" !== r && (t = t.replace(/\n/g, r)), yield ee(e, t);
  })), function e(t, r) {
   return D.apply(this, arguments);
  }), t.hardlinksWork = (R = (0, (p || n()).default)((function*(e) {
   const t = "test-file" + Math.random(), r = (b || s()).default.join(e, t), n = (b || s()).default.join(e, t + "-link");
   try {
    yield ee(r, "test"), yield ce(r, n);
   } catch (e) {
    return !1;
   } finally {
    yield (0, (E || l()).unlink)(r), yield (0, (E || l()).unlink)(n);
   }
   return !0;
  })), function e(t) {
   return R.apply(this, arguments);
  }), t.makeTempDir = (M = (0, (p || n()).default)((function*(e) {
   const t = (b || s()).default.join((y || o()).default.tmpdir(), `yarn-${e || ""}-${Date.now()}-${Math.random()}`);
   return yield (0, (E || l()).unlink)(t), yield oe(t), t;
  })), function e(t) {
   return M.apply(this, arguments);
  }), t.readFirstAvailableStream = (I = (0, (p || n()).default)((function*(e) {
   var t, r, n, o;
   for (t = e, n = 0, t = (r = Array.isArray(t)) ? t : t[Symbol.iterator](); ;) {
    if (r) {
     if (n >= t.length) break;
     o = t[n++];
    } else {
     if ((n = t.next()).done) break;
     o = n.value;
    }
    const e = o;
    try {
     const t = yield Z(e, "r");
     return (m || i()).default.createReadStream(e, {
      fd: t
     });
    } catch (e) {}
   }
   return null;
  })), function e(t) {
   return I.apply(this, arguments);
  }), t.getFirstSuitableFolder = (q = (0, (p || n()).default)((function*(e, t = Q.W_OK | Q.X_OK) {
   var r, n, i, o;
   const s = {
    skipped: [],
    folder: null
   };
   for (r = e, i = 0, r = (n = Array.isArray(r)) ? r : r[Symbol.iterator](); ;) {
    if (n) {
     if (i >= r.length) break;
     o = r[i++];
    } else {
     if ((i = r.next()).done) break;
     o = i.value;
    }
    const e = o;
    try {
     return yield oe(e), yield ie(e, t), s.folder = e, s;
    } catch (t) {
     s.skipped.push({
      error: t,
      folder: e
     });
    }
   }
   return s;
  })), function e(t) {
   return q.apply(this, arguments);
  }), t.copy = function V(e, t, r) {
   return z([ {
    src: e,
    dest: t
   } ], r);
  }, t.readFile = h, t.readFileRaw = function Y(e) {
   return f(e, "binary");
  }, t.normalizeOS = d;
  const Q = t.constants = void 0 !== (m || i()).default.constants ? (m || i()).default.constants : {
   R_OK: (m || i()).default.R_OK,
   W_OK: (m || i()).default.W_OK,
   X_OK: (m || i()).default.X_OK
  };
  t.lockQueue = new ((v || function X() {
   return v = u(r(84));
  }()).default)("fs lock");
  const J = t.readFileBuffer = (0, (w || c()).promisify)((m || i()).default.readFile), Z = t.open = (0, 
  (w || c()).promisify)((m || i()).default.open), ee = t.writeFile = (0, (w || c()).promisify)((m || i()).default.writeFile), te = t.readlink = (0, 
  (w || c()).promisify)((m || i()).default.readlink), re = t.realpath = (0, (w || c()).promisify)((m || i()).default.realpath), ne = t.readdir = (0, 
  (w || c()).promisify)((m || i()).default.readdir);
  t.rename = (0, (w || c()).promisify)((m || i()).default.rename);
  const ie = t.access = (0, (w || c()).promisify)((m || i()).default.access);
  t.stat = (0, (w || c()).promisify)((m || i()).default.stat);
  const oe = t.mkdirp = (0, (w || c()).promisify)(r(116)), se = t.exists = (0, (w || c()).promisify)((m || i()).default.exists, !0), ae = t.lstat = (0, 
  (w || c()).promisify)((m || i()).default.lstat);
  t.chmod = (0, (w || c()).promisify)((m || i()).default.chmod);
  const ce = t.link = (0, (w || c()).promisify)((m || i()).default.link);
  t.glob = (0, (w || c()).promisify)((g || function le() {
   return g = u(r(75));
  }()).default), t.unlink = (E || l()).unlink;
  const ue = (m || i()).default.copyFile ? 128 : 4, fe = (0, (w || c()).promisify)((m || i()).default.symlink), he = r(7), de = r(122), pe = () => {}, me = "\r".charCodeAt(0), ge = "\n".charCodeAt(0);
 }, function(e, t, r) {
  function n(e, t) {
   let r = "PATH";
   if ("win32" === e) {
    r = "Path";
    for (const e in t) "path" === e.toLowerCase() && (r = e);
   }
   return r;
  }
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.getPathKey = n;
  const i = r(36), o = r(0), s = r(45).default;
  var a = r(171);
  const c = a.getCacheDir, l = a.getConfigDir, u = a.getDataDir, f = r(227), h = t.DEPENDENCY_TYPES = [ "devDependencies", "dependencies", "optionalDependencies", "peerDependencies" ], d = t.RESOLUTIONS = "resolutions";
  t.MANIFEST_FIELDS = [ d, ...h ], t.SUPPORTED_NODE_VERSIONS = "^4.8.0 || ^5.7.0 || ^6.2.2 || >=8.0.0", 
  t.YARN_REGISTRY = "https://registry.yarnpkg.com", t.YARN_DOCS = "https://yarnpkg.com/en/docs/cli/", 
  t.YARN_INSTALLER_SH = "https://yarnpkg.com/install.sh", t.YARN_INSTALLER_MSI = "https://yarnpkg.com/latest.msi", 
  t.SELF_UPDATE_VERSION_URL = "https://yarnpkg.com/latest-version", t.CACHE_VERSION = 2, 
  t.LOCKFILE_VERSION = 1, t.NETWORK_CONCURRENCY = 8, t.NETWORK_TIMEOUT = 3e4, t.CHILD_CONCURRENCY = 5, 
  t.REQUIRED_PACKAGE_KEYS = [ "name", "version", "_uid" ], t.PREFERRED_MODULE_CACHE_DIRECTORIES = function p() {
   const e = [ c() ];
   return process.getuid && e.push(o.join(i.tmpdir(), `.yarn-cache-${process.getuid()}`)), 
   e.push(o.join(i.tmpdir(), ".yarn-cache")), e;
  }(), t.CONFIG_DIRECTORY = l();
  const m = t.DATA_DIRECTORY = u();
  t.LINK_REGISTRY_DIRECTORY = o.join(m, "link"), t.GLOBAL_MODULE_DIRECTORY = o.join(m, "global"), 
  t.NODE_BIN_PATH = process.execPath, t.YARN_BIN_PATH = function g() {
   return f ? __filename : o.join(__dirname, "..", "bin", "yarn.js");
  }(), t.NODE_MODULES_FOLDER = "node_modules", t.NODE_PACKAGE_JSON = "package.json", 
  t.POSIX_GLOBAL_PREFIX = `${process.env.DESTDIR || ""}/usr/local`, t.FALLBACK_GLOBAL_PREFIX = o.join(s, ".yarn"), 
  t.META_FOLDER = ".yarn-meta", t.INTEGRITY_FILENAME = ".yarn-integrity", t.LOCKFILE_FILENAME = "yarn.lock", 
  t.METADATA_FILENAME = ".yarn-metadata.json", t.TARBALL_FILENAME = ".yarn-tarball.tgz", 
  t.CLEAN_FILENAME = ".yarnclean", t.NPM_LOCK_FILENAME = "package-lock.json", t.NPM_SHRINKWRAP_FILENAME = "npm-shrinkwrap.json", 
  t.DEFAULT_INDENT = "  ", t.SINGLE_INSTANCE_PORT = 31997, t.SINGLE_INSTANCE_FILENAME = ".yarn-single-instance", 
  t.ENV_PATH_KEY = n(process.platform, process.env), t.VERSION_COLOR_SCHEME = {
   major: "red",
   premajor: "red",
   minor: "yellow",
   preminor: "yellow",
   patch: "green",
   prepatch: "green",
   prerelease: "red",
   unchanged: "white",
   unknown: "red"
  };
 }, function(e, t, r) {
  var n = process.env.NODE_ENV;
  e.exports = function(e, t, r, i, o, s, a, c) {
   var l, u, f;
   if ("production" !== n && void 0 === t) throw new Error("invariant requires an error message argument");
   if (!e) throw void 0 === t ? l = new Error("Minified exception occurred; use the non-minified dev environment for the full error message and additional helpful warnings.") : (u = [ r, i, o, s, a, c ], 
   f = 0, (l = new Error(t.replace(/%s/g, (function() {
    return u[f++];
   })))).name = "Invariant Violation"), l.framesToPop = 1, l;
  };
 }, , function(e, t) {
  e.exports = require$$3__default.default;
 }, , function(e, t) {
  var r = e.exports = "undefined" != typeof window && window.Math == Math ? window : "undefined" != typeof self && self.Math == Math ? self : Function("return this")();
  "number" == typeof __g && (__g = r);
 }, function(e, t, r) {
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.sortAlpha = function n(e, t) {
   const r = Math.min(e.length, t.length);
   for (let n = 0; n < r; n++) {
    const r = e.charCodeAt(n), i = t.charCodeAt(n);
    if (r !== i) return r - i;
   }
   return e.length - t.length;
  }, t.entries = function i(e) {
   const t = [];
   if (e) for (const r in e) t.push([ r, e[r] ]);
   return t;
  }, t.removePrefix = function o(e, t) {
   return e.startsWith(t) && (e = e.slice(t.length)), e;
  }, t.removeSuffix = function s(e, t) {
   return e.endsWith(t) ? e.slice(0, -t.length) : e;
  }, t.addSuffix = function a(e, t) {
   return e.endsWith(t) ? e : e + t;
  }, t.hyphenate = function c(e) {
   return e.replace(/[A-Z]/g, (e => "-" + e.charAt(0).toLowerCase()));
  }, t.camelCase = function l(e) {
   return /[A-Z]/.test(e) ? null : h(e);
  }, t.compareSortedArrays = function u(e, t) {
   if (e.length !== t.length) return !1;
   for (let r = 0, n = e.length; r < n; r++) if (e[r] !== t[r]) return !1;
   return !0;
  }, t.sleep = function f(e) {
   return new Promise((t => {
    setTimeout(t, e);
   }));
  };
  const h = r(176);
 }, function(e, t, r) {
  var n = r(107)("wks"), i = r(111), o = r(11).Symbol, s = "function" == typeof o;
  (e.exports = function(e) {
   return n[e] || (n[e] = s && o[e] || (s ? o : i)("Symbol." + e));
  }).store = n;
 }, function(e, t, r) {
  function n() {
   return y = function e(t) {
    var r, n;
    if (t && t.__esModule) return t;
    if (r = {}, null != t) for (n in t) Object.prototype.hasOwnProperty.call(t, n) && (r[n] = t[n]);
    return r.default = t, r;
   }(r(5));
  }
  function i(e) {
   return e && e.__esModule ? e : {
    default: e
   };
  }
  function o(e) {
   return (0, (p || function t() {
    return p = r(29);
   }()).normalizePattern)(e).name;
  }
  function s(e) {
   return e && Object.keys(e).length ? e : void 0;
  }
  function a(e) {
   return e.resolved || (e.reference && e.hash ? `${e.reference}#${e.hash}` : null);
  }
  function c(e, t) {
   const r = o(e), n = t.integrity ? function i(e) {
    return e.toString().split(" ").sort().join(" ");
   }(t.integrity) : "", a = {
    name: r === t.name ? void 0 : t.name,
    version: t.version,
    uid: t.uid === t.version ? void 0 : t.uid,
    resolved: t.resolved,
    registry: "npm" === t.registry ? void 0 : t.registry,
    dependencies: s(t.dependencies),
    optionalDependencies: s(t.optionalDependencies),
    permissions: s(t.permissions),
    prebuiltVariants: s(t.prebuiltVariants)
   };
   return n && (a.integrity = n), a;
  }
  function l(e, t) {
   t.optionalDependencies = t.optionalDependencies || {}, t.dependencies = t.dependencies || {}, 
   t.uid = t.uid || t.version, t.permissions = t.permissions || {}, t.registry = t.registry || "npm", 
   t.name = t.name || o(e);
   const r = t.integrity;
   return r && r.isIntegrity && (t.integrity = _.parse(r)), t;
  }
  var u, f, h, d, p, m, g, y;
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.stringify = t.parse = void 0, Object.defineProperty(t, "parse", {
   enumerable: !0,
   get: function e() {
    return i(f || function t() {
     return f = r(81);
    }()).default;
   }
  }), Object.defineProperty(t, "stringify", {
   enumerable: !0,
   get: function e() {
    return i(h || function t() {
     return h = r(150);
    }()).default;
   }
  }), t.implodeEntry = c, t.explodeEntry = l;
  const b = r(7), v = r(0), _ = r(55);
  class w {
   constructor({cache: e, source: t, parseResultType: r} = {}) {
    this.source = t || "", this.cache = e, this.parseResultType = r;
   }
   hasEntriesExistWithoutIntegrity() {
    if (!this.cache) return !1;
    for (const e in this.cache) if (!/^.*@(file:|http)/.test(e) && this.cache[e] && !this.cache[e].integrity) return !0;
    return !1;
   }
   static fromDirectory(e, t) {
    return (0, (u || function o() {
     return u = i(r(1));
    }()).default)((function*() {
     const o = v.join(e, (g || function s() {
      return g = r(6);
     }()).LOCKFILE_FILENAME);
     let a, c, l = "";
     return (yield (y || n()).exists(o)) ? (l = yield (y || n()).readFile(o), c = (0, 
     (m || function u() {
      return m = i(r(81));
     }()).default)(l, o), t && ("merge" === c.type ? t.info(t.lang("lockfileMerged")) : "conflict" === c.type && t.warn(t.lang("lockfileConflict"))), 
     a = c.object) : t && t.info(t.lang("noLockfileFound")), new w({
      cache: a,
      source: l,
      parseResultType: c && c.type
     });
    }))();
   }
   getLocked(e) {
    const t = this.cache;
    if (!t) return;
    const r = e in t && t[e];
    return "string" == typeof r ? this.getLocked(r) : r ? (l(e, r), r) : void 0;
   }
   removePattern(e) {
    const t = this.cache;
    t && delete t[e];
   }
   getLockfile(e) {
    var t, n, i, s;
    const l = {}, u = new Map;
    for (t = Object.keys(e).sort((d || function f() {
     return d = r(12);
    }()).sortAlpha), i = 0, t = (n = Array.isArray(t)) ? t : t[Symbol.iterator](); ;) {
     if (n) {
      if (i >= t.length) break;
      s = t[i++];
     } else {
      if ((i = t.next()).done) break;
      s = i.value;
     }
     const r = s, f = e[r], h = f._remote, d = f._reference;
     b(d, "Package is missing a reference"), b(h, "Package is missing a remote");
     const p = a(h), m = p && u.get(p);
     if (m) {
      l[r] = m, m.name || o(r) === f.name || (m.name = f.name);
      continue;
     }
     const g = c(r, {
      name: f.name,
      version: f.version,
      uid: f._uid,
      resolved: h.resolved,
      integrity: h.integrity,
      registry: h.registry,
      dependencies: f.dependencies,
      peerDependencies: f.peerDependencies,
      optionalDependencies: f.optionalDependencies,
      permissions: d.permissions,
      prebuiltVariants: f.prebuiltVariants
     });
     l[r] = g, p && u.set(p, g);
    }
    return l;
   }
  }
  t.default = w;
 }, , , function(e, t) {
  e.exports = require$$4__default.default;
 }, , , function(e, t, r) {
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.default = function e(t = {}) {
   var r, n, i, o;
   if (Array.isArray(t)) for (r = t, i = 0, r = (n = Array.isArray(r)) ? r : r[Symbol.iterator](); ;) {
    if (n) {
     if (i >= r.length) break;
     o = r[i++];
    } else {
     if ((i = r.next()).done) break;
     o = i.value;
    }
    e(o);
   } else if ((null !== t && "object" == typeof t || "function" == typeof t) && (Object.setPrototypeOf(t, null), 
   "object" == typeof t)) for (const r in t) e(t[r]);
   return t;
  };
 }, , function(e, t) {
  e.exports = require$$5__default.default;
 }, function(e, t) {
  var r = e.exports = {
   version: "2.5.7"
  };
  "number" == typeof __e && (__e = r);
 }, , , , function(e, t, r) {
  var n = r(34);
  e.exports = function(e) {
   if (!n(e)) throw TypeError(e + " is not an object!");
   return e;
  };
 }, , function(e, t, r) {
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.normalizePattern = function n(e) {
   let t = !1, r = "latest", n = e, i = !1;
   "@" === n[0] && (i = !0, n = n.slice(1));
   const o = n.split("@");
   return o.length > 1 && (n = o.shift(), r = o.join("@"), r ? t = !0 : r = "*"), i && (n = `@${n}`), 
   {
    name: n,
    range: r,
    hasVersion: t
   };
  };
 }, , function(e, t, r) {
  var n = r(50), i = r(106);
  e.exports = r(33) ? function(e, t, r) {
   return n.f(e, t, i(1, r));
  } : function(e, t, r) {
   return e[t] = r, e;
  };
 }, function(e, t, r) {
  function n(e, t) {
   for (var r in e) t[r] = e[r];
  }
  function i(e, t, r) {
   return s(e, t, r);
  }
  var o = r(63), s = o.Buffer;
  s.from && s.alloc && s.allocUnsafe && s.allocUnsafeSlow ? e.exports = o : (n(o, t), 
  t.Buffer = i), n(s, i), i.from = function(e, t, r) {
   if ("number" == typeof e) throw new TypeError("Argument must not be a number");
   return s(e, t, r);
  }, i.alloc = function(e, t, r) {
   if ("number" != typeof e) throw new TypeError("Argument must be a number");
   var n = s(e);
   return void 0 !== t ? "string" == typeof r ? n.fill(t, r) : n.fill(t) : n.fill(0), 
   n;
  }, i.allocUnsafe = function(e) {
   if ("number" != typeof e) throw new TypeError("Argument must be a number");
   return s(e);
  }, i.allocUnsafeSlow = function(e) {
   if ("number" != typeof e) throw new TypeError("Argument must be a number");
   return o.SlowBuffer(e);
  };
 }, function(e, t, r) {
  e.exports = !r(85)((function() {
   return 7 != Object.defineProperty({}, "a", {
    get: function() {
     return 7;
    }
   }).a;
  }));
 }, function(e, t) {
  e.exports = function(e) {
   return "object" == typeof e ? null !== e : "function" == typeof e;
  };
 }, function(e, t) {
  e.exports = {};
 }, function(e, t) {
  e.exports = require$$6__default.default;
 }, , , , function(e, t, r) {
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.wait = function n(e) {
   return new Promise((t => {
    setTimeout(t, e);
   }));
  }, t.promisify = function i(e, t) {
   return function(...r) {
    return new Promise((function(n, i) {
     r.push((function(e, ...r) {
      let o = r;
      r.length <= 1 && (o = r[0]), t && (o = e, e = null), e ? i(e) : n(o);
     })), e.apply(null, r);
    }));
   };
  }, t.queue = function o(e, t, r = 1 / 0) {
   r = Math.min(r, e.length), e = e.slice();
   const n = [];
   let i = e.length;
   return i ? new Promise(((o, s) => {
    function a() {
     const r = e.shift();
     t(r).then((function(t) {
      n.push(t), i--, 0 === i ? o(n) : e.length && a();
     }), s);
    }
    for (let e = 0; e < r; e++) a();
   })) : Promise.resolve(n);
  };
 }, function(e, t, r) {
  var n = r(11), i = r(23), o = r(48), s = r(31), a = r(49), c = function(e, t, r) {
   var l, u, f, h = e & c.F, d = e & c.G, p = e & c.S, m = e & c.P, g = e & c.B, y = e & c.W, b = d ? i : i[t] || (i[t] = {}), v = b.prototype, _ = d ? n : p ? n[t] : (n[t] || {}).prototype;
   for (l in d && (r = t), r) (u = !h && _ && void 0 !== _[l]) && a(b, l) || (f = u ? _[l] : r[l], 
   b[l] = d && "function" != typeof _[l] ? r[l] : g && u ? o(f, n) : y && _[l] == f ? function(e) {
    var t = function(t, r, n) {
     if (this instanceof e) {
      switch (arguments.length) {
      case 0:
       return new e;

      case 1:
       return new e(t);

      case 2:
       return new e(t, r);
      }
      return new e(t, r, n);
     }
     return e.apply(this, arguments);
    };
    return t.prototype = e.prototype, t;
   }(f) : m && "function" == typeof f ? o(Function.call, f) : f, m && ((b.virtual || (b.virtual = {}))[l] = f, 
   e & c.R && v && !v[l] && s(v, l, f)));
  };
  c.F = 1, c.G = 2, c.S = 4, c.P = 8, c.B = 16, c.W = 32, c.U = 64, c.R = 128, e.exports = c;
 }, function(e, t, r) {
  try {
   var n = r(2);
   if ("function" != typeof n.inherits) throw "";
   e.exports = n.inherits;
  } catch (t) {
   e.exports = r(224);
  }
 }, , , function(e, t, r) {
  var n;
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.home = void 0;
  const i = r(0), o = t.home = r(36).homedir(), s = (n || function a() {
   return n = function e(t) {
    return t && t.__esModule ? t : {
     default: t
    };
   }(r(169));
  }()).default ? i.resolve("/usr/local/share") : o;
  t.default = s;
 }, function(e, t) {
  e.exports = function(e) {
   if ("function" != typeof e) throw TypeError(e + " is not a function!");
   return e;
  };
 }, function(e, t) {
  var r = {}.toString;
  e.exports = function(e) {
   return r.call(e).slice(8, -1);
  };
 }, function(e, t, r) {
  var n = r(46);
  e.exports = function(e, t, r) {
   if (n(e), void 0 === t) return e;
   switch (r) {
   case 1:
    return function(r) {
     return e.call(t, r);
    };

   case 2:
    return function(r, n) {
     return e.call(t, r, n);
    };

   case 3:
    return function(r, n, i) {
     return e.call(t, r, n, i);
    };
   }
   return function() {
    return e.apply(t, arguments);
   };
  };
 }, function(e, t) {
  var r = {}.hasOwnProperty;
  e.exports = function(e, t) {
   return r.call(e, t);
  };
 }, function(e, t, r) {
  var n = r(27), i = r(184), o = r(201), s = Object.defineProperty;
  t.f = r(33) ? Object.defineProperty : function e(t, r, a) {
   if (n(t), r = o(r, !0), n(a), i) try {
    return s(t, r, a);
   } catch (e) {}
   if ("get" in a || "set" in a) throw TypeError("Accessors not supported!");
   return "value" in a && (t[r] = a.value), t;
  };
 }, , , , function(e, t) {
  e.exports = require$$7__default.default;
 }, function(e, t, r) {
  function n(e, t) {
   if (t = t || {}, "string" == typeof e) return i(e, t);
   if (e.algorithm && e.digest) {
    const r = new y;
    return r[e.algorithm] = [ e ], i(o(r, t), t);
   }
   return i(o(e, t), t);
  }
  function i(e, t) {
   return t.single ? new g(e, t) : e.trim().split(/\s+/).reduce(((e, r) => {
    const n = new g(r, t);
    if (n.algorithm && n.digest) {
     const t = n.algorithm;
     e[t] || (e[t] = []), e[t].push(n);
    }
    return e;
   }), new y);
  }
  function o(e, t) {
   return e.algorithm && e.digest ? g.prototype.toString.call(e, t) : "string" == typeof e ? o(n(e, t), t) : y.prototype.toString.call(e, t);
  }
  function s(e) {
   const t = (e = e || {}).integrity && n(e.integrity, e), r = t && Object.keys(t).length, i = r && t.pickAlgorithm(e), o = r && t[i], s = Array.from(new Set((e.algorithms || [ "sha512" ]).concat(i ? [ i ] : []))), a = s.map(l.createHash);
   let c = 0;
   const f = new u({
    transform(e, t, r) {
     c += e.length, a.forEach((r => r.update(e, t))), r(null, e, t);
    }
   }).on("end", (() => {
    const l = e.options && e.options.length ? `?${e.options.join("?")}` : "", u = n(a.map(((e, t) => `${s[t]}-${e.digest("base64")}${l}`)).join(" "), e), h = r && u.match(t, e);
    if ("number" == typeof e.size && c !== e.size) {
     const r = new Error(`stream size mismatch when checking ${t}.\n  Wanted: ${e.size}\n  Found: ${c}`);
     r.code = "EBADSIZE", r.found = c, r.expected = e.size, r.sri = t, f.emit("error", r);
    } else if (e.integrity && !h) {
     const e = new Error(`${t} integrity checksum failed when using ${i}: wanted ${o} but got ${u}. (${c} bytes)`);
     e.code = "EINTEGRITY", e.found = u, e.expected = o, e.algorithm = i, e.sri = t, 
     f.emit("error", e);
    } else f.emit("size", c), f.emit("integrity", u), h && f.emit("verified", h);
   }));
   return f;
  }
  function a(e, t) {
   return O.indexOf(e.toLowerCase()) >= O.indexOf(t.toLowerCase()) ? e : t;
  }
  const c = r(32).Buffer, l = r(9), u = r(17).Transform, f = [ "sha256", "sha384", "sha512" ], h = /^[a-z0-9+/]+(?:=?=?)$/i, d = /^([^-]+)-([^?]+)([?\S*]*)$/, p = /^([^-]+)-([A-Za-z0-9+/=]{44,88})(\?[\x21-\x7E]*)*$/, m = /^[\x21-\x7E]+$/;
  class g {
   get isHash() {
    return !0;
   }
   constructor(e, t) {
    const r = !(!t || !t.strict);
    this.source = e.trim();
    const n = this.source.match(r ? p : d);
    if (!n) return;
    if (r && !f.some((e => e === n[1]))) return;
    this.algorithm = n[1], this.digest = n[2];
    const i = n[3];
    this.options = i ? i.slice(1).split("?") : [];
   }
   hexDigest() {
    return this.digest && c.from(this.digest, "base64").toString("hex");
   }
   toJSON() {
    return this.toString();
   }
   toString(e) {
    if (e && e.strict && !(f.some((e => e === this.algorithm)) && this.digest.match(h) && (this.options || []).every((e => e.match(m))))) return "";
    const t = this.options && this.options.length ? `?${this.options.join("?")}` : "";
    return `${this.algorithm}-${this.digest}${t}`;
   }
  }
  class y {
   get isIntegrity() {
    return !0;
   }
   toJSON() {
    return this.toString();
   }
   toString(e) {
    let t = (e = e || {}).sep || " ";
    return e.strict && (t = t.replace(/\S+/g, " ")), Object.keys(this).map((r => this[r].map((t => g.prototype.toString.call(t, e))).filter((e => e.length)).join(t))).filter((e => e.length)).join(t);
   }
   concat(e, t) {
    const r = "string" == typeof e ? e : o(e, t);
    return n(`${this.toString(t)} ${r}`, t);
   }
   hexDigest() {
    return n(this, {
     single: !0
    }).hexDigest();
   }
   match(e, t) {
    const r = n(e, t), i = r.pickAlgorithm(t);
    return this[i] && r[i] && this[i].find((e => r[i].find((t => e.digest === t.digest)))) || !1;
   }
   pickAlgorithm(e) {
    const t = e && e.pickAlgorithm || a, r = Object.keys(this);
    if (!r.length) throw new Error(`No algorithms available for ${JSON.stringify(this.toString())}`);
    return r.reduce(((e, r) => t(e, r) || e));
   }
  }
  e.exports.parse = n, e.exports.stringify = o, e.exports.fromHex = function b(e, t, r) {
   const i = r && r.options && r.options.length ? `?${r.options.join("?")}` : "";
   return n(`${t}-${c.from(e, "hex").toString("base64")}${i}`, r);
  }, e.exports.fromData = function v(e, t) {
   const r = (t = t || {}).algorithms || [ "sha512" ], n = t.options && t.options.length ? `?${t.options.join("?")}` : "";
   return r.reduce(((r, i) => {
    const o = l.createHash(i).update(e).digest("base64"), s = new g(`${i}-${o}${n}`, t);
    if (s.algorithm && s.digest) {
     const e = s.algorithm;
     r[e] || (r[e] = []), r[e].push(s);
    }
    return r;
   }), new y);
  }, e.exports.fromStream = function _(e, t) {
   const r = (t = t || {}).Promise || Promise, n = s(t);
   return new r(((t, r) => {
    let i;
    e.pipe(n), e.on("error", r), n.on("error", r), n.on("integrity", (e => {
     i = e;
    })), n.on("end", (() => t(i))), n.on("data", (() => {}));
   }));
  }, e.exports.checkData = function w(e, t, r) {
   if (t = n(t, r = r || {}), !Object.keys(t).length) {
    if (r.error) throw Object.assign(new Error("No valid integrity hashes to check against"), {
     code: "EINTEGRITY"
    });
    return !1;
   }
   const i = t.pickAlgorithm(r), o = n({
    algorithm: i,
    digest: l.createHash(i).update(e).digest("base64")
   }), s = o.match(t, r);
   if (s || !r.error) return s;
   if ("number" == typeof r.size && e.length !== r.size) {
    const n = new Error(`data size mismatch when checking ${t}.\n  Wanted: ${r.size}\n  Found: ${e.length}`);
    throw n.code = "EBADSIZE", n.found = e.length, n.expected = r.size, n.sri = t, n;
   }
   {
    const r = new Error(`Integrity checksum failed when using ${i}: Wanted ${t}, but got ${o}. (${e.length} bytes)`);
    throw r.code = "EINTEGRITY", r.found = o, r.expected = t, r.algorithm = i, r.sri = t, 
    r;
   }
  }, e.exports.checkStream = function k(e, t, r) {
   const n = (r = r || {}).Promise || Promise, i = s(Object.assign({}, r, {
    integrity: t
   }));
   return new n(((t, r) => {
    let n;
    e.pipe(i), e.on("error", r), i.on("error", r), i.on("verified", (e => {
     n = e;
    })), i.on("end", (() => t(n))), i.on("data", (() => {}));
   }));
  }, e.exports.integrityStream = s, e.exports.create = function E(e) {
   const t = (e = e || {}).algorithms || [ "sha512" ], r = e.options && e.options.length ? `?${e.options.join("?")}` : "", n = t.map(l.createHash);
   return {
    update: function(e, t) {
     return n.forEach((r => r.update(e, t))), this;
    },
    digest: function(i) {
     return t.reduce(((t, i) => {
      const o = n.shift().digest("base64"), s = new g(`${i}-${o}${r}`, e);
      if (s.algorithm && s.digest) {
       const e = s.algorithm;
       t[e] || (t[e] = []), t[e].push(s);
      }
      return t;
     }), new y);
    }
   };
  };
  const x = new Set(l.getHashes()), O = [ "md5", "whirlpool", "sha1", "sha224", "sha256", "sha384", "sha512", "sha3", "sha3-256", "sha3-384", "sha3-512", "sha3_256", "sha3_384", "sha3_512" ].filter((e => x.has(e)));
 }, , , , , function(e, t, r) {
  function n(e, t) {
   e = e || {}, t = t || {};
   var r = {};
   return Object.keys(t).forEach((function(e) {
    r[e] = t[e];
   })), Object.keys(e).forEach((function(t) {
    r[t] = e[t];
   })), r;
  }
  function i(e, t, r) {
   if ("string" != typeof t) throw new TypeError("glob pattern string required");
   return r || (r = {}), !(!r.nocomment && "#" === t.charAt(0)) && ("" === t.trim() ? "" === e : new o(t, r).match(e));
  }
  function o(e, t) {
   if (!(this instanceof o)) return new o(e, t);
   if ("string" != typeof e) throw new TypeError("glob pattern string required");
   t || (t = {}), e = e.trim(), "/" !== a.sep && (e = e.split(a.sep).join("/")), this.options = t, 
   this.set = [], this.pattern = e, this.regexp = null, this.negate = !1, this.comment = !1, 
   this.empty = !1, this.make();
  }
  function s(e, t) {
   if (t || (t = this instanceof o ? this.options : {}), void 0 === (e = void 0 === e ? this.pattern : e)) throw new TypeError("undefined pattern");
   return t.nobrace || !e.match(/\{.*\}/) ? [ e ] : l(e);
  }
  var a, c, l, u, f, h, d, p, m;
  e.exports = i, i.Minimatch = o, a = {
   sep: "/"
  };
  try {
   a = r(0);
  } catch (e) {}
  c = i.GLOBSTAR = o.GLOBSTAR = {}, l = r(175), u = {
   "!": {
    open: "(?:(?!(?:",
    close: "))[^/]*?)"
   },
   "?": {
    open: "(?:",
    close: ")?"
   },
   "+": {
    open: "(?:",
    close: ")+"
   },
   "*": {
    open: "(?:",
    close: ")*"
   },
   "@": {
    open: "(?:",
    close: ")"
   }
  }, h = (f = "[^/]") + "*?", d = function g(e) {
   return e.split("").reduce((function(e, t) {
    return e[t] = !0, e;
   }), {});
  }("().*{}+?[]^$\\!"), p = /\/+/, i.filter = function y(e, t) {
   return t = t || {}, function(r, n, o) {
    return i(r, e, t);
   };
  }, i.defaults = function(e) {
   var t, r;
   return e && Object.keys(e).length ? (t = i, (r = function r(i, o, s) {
    return t.minimatch(i, o, n(e, s));
   }).Minimatch = function r(i, o) {
    return new t.Minimatch(i, n(e, o));
   }, r) : i;
  }, o.defaults = function(e) {
   return e && Object.keys(e).length ? i.defaults(e).Minimatch : o;
  }, o.prototype.debug = function() {}, o.prototype.make = function b() {
   var e, t, r;
   this._made || (e = this.pattern, (t = this.options).nocomment || "#" !== e.charAt(0) ? e ? (this.parseNegate(), 
   r = this.globSet = this.braceExpand(), t.debug && (this.debug = console.error), 
   this.debug(this.pattern, r), r = this.globParts = r.map((function(e) {
    return e.split(p);
   })), this.debug(this.pattern, r), r = r.map((function(e, t, r) {
    return e.map(this.parse, this);
   }), this), this.debug(this.pattern, r), r = r.filter((function(e) {
    return -1 === e.indexOf(!1);
   })), this.debug(this.pattern, r), this.set = r) : this.empty = !0 : this.comment = !0);
  }, o.prototype.parseNegate = function v() {
   var e, t, r = this.pattern, n = !1, i = 0;
   if (!this.options.nonegate) {
    for (e = 0, t = r.length; e < t && "!" === r.charAt(e); e++) n = !n, i++;
    i && (this.pattern = r.substr(i)), this.negate = n;
   }
  }, i.braceExpand = function(e, t) {
   return s(e, t);
  }, o.prototype.braceExpand = s, o.prototype.parse = function _(e, t) {
   function r() {
    if (p) {
     switch (p) {
     case "*":
      i += h, o = !0;
      break;

     case "?":
      i += f, o = !0;
      break;

     default:
      i += "\\" + p;
     }
     _.debug("clearStateChar %j %j", p, i), p = !1;
    }
   }
   var n, i, o, s, a, l, p, g, y, b, v, _, w, k, E, x, O, S, C, A, j, T, P, F, L, $, N, D, R, M, I, q;
   if (e.length > 65536) throw new TypeError("pattern is too long");
   if (!(n = this.options).noglobstar && "**" === e) return c;
   if ("" === e) return "";
   for (i = "", o = !!n.nocase, s = !1, a = [], l = [], g = !1, y = -1, b = -1, v = "." === e.charAt(0) ? "" : n.dot ? "(?!(?:^|\\/)\\.{1,2}(?:$|\\/))" : "(?!\\.)", 
   _ = this, w = 0, k = e.length; w < k && (E = e.charAt(w)); w++) if (this.debug("%s\t%s %s %j", e, w, i, E), 
   s && d[E]) i += "\\" + E, s = !1; else switch (E) {
   case "/":
    return !1;

   case "\\":
    r(), s = !0;
    continue;

   case "?":
   case "*":
   case "+":
   case "@":
   case "!":
    if (this.debug("%s\t%s %s %j <-- stateChar", e, w, i, E), g) {
     this.debug("  in class"), "!" === E && w === b + 1 && (E = "^"), i += E;
     continue;
    }
    _.debug("call clearStateChar %j", p), r(), p = E, n.noext && r();
    continue;

   case "(":
    if (g) {
     i += "(";
     continue;
    }
    if (!p) {
     i += "\\(";
     continue;
    }
    a.push({
     type: p,
     start: w - 1,
     reStart: i.length,
     open: u[p].open,
     close: u[p].close
    }), i += "!" === p ? "(?:(?!(?:" : "(?:", this.debug("plType %j %j", p, i), p = !1;
    continue;

   case ")":
    if (g || !a.length) {
     i += "\\)";
     continue;
    }
    r(), o = !0, x = a.pop(), i += x.close, "!" === x.type && l.push(x), x.reEnd = i.length;
    continue;

   case "|":
    if (g || !a.length || s) {
     i += "\\|", s = !1;
     continue;
    }
    r(), i += "|";
    continue;

   case "[":
    if (r(), g) {
     i += "\\" + E;
     continue;
    }
    g = !0, b = w, y = i.length, i += E;
    continue;

   case "]":
    if (w === b + 1 || !g) {
     i += "\\" + E, s = !1;
     continue;
    }
    if (g) {
     O = e.substring(b + 1, w);
     try {
      RegExp("[" + O + "]");
     } catch (e) {
      S = this.parse(O, m), i = i.substr(0, y) + "\\[" + S[0] + "\\]", o = o || S[1], 
      g = !1;
      continue;
     }
    }
    o = !0, g = !1, i += E;
    continue;

   default:
    r(), s ? s = !1 : !d[E] || "^" === E && g || (i += "\\"), i += E;
   }
   for (g && (O = e.substr(b + 1), S = this.parse(O, m), i = i.substr(0, y) + "\\[" + S[0], 
   o = o || S[1]), x = a.pop(); x; x = a.pop()) C = i.slice(x.reStart + x.open.length), 
   this.debug("setting tail", i, x), C = C.replace(/((?:\\{2}){0,64})(\\?)\|/g, (function(e, t, r) {
    return r || (r = "\\"), t + t + r + "|";
   })), this.debug("tail=%j\n   %s", C, C, x, i), A = "*" === x.type ? h : "?" === x.type ? f : "\\" + x.type, 
   o = !0, i = i.slice(0, x.reStart) + A + "\\(" + C;
   switch (r(), s && (i += "\\\\"), j = !1, i.charAt(0)) {
   case ".":
   case "[":
   case "(":
    j = !0;
   }
   for (T = l.length - 1; T > -1; T--) {
    for (P = l[T], F = i.slice(0, P.reStart), L = i.slice(P.reStart, P.reEnd - 8), $ = i.slice(P.reEnd - 8, P.reEnd), 
    $ += N = i.slice(P.reEnd), D = F.split("(").length - 1, R = N, w = 0; w < D; w++) R = R.replace(/\)[+*?]?/, "");
    M = "", "" === (N = R) && t !== m && (M = "$"), i = F + L + N + M + $;
   }
   if ("" !== i && o && (i = "(?=.)" + i), j && (i = v + i), t === m) return [ i, o ];
   if (!o) return function G(e) {
    return e.replace(/\\(.)/g, "$1");
   }(e);
   I = n.nocase ? "i" : "";
   try {
    q = new RegExp("^" + i + "$", I);
   } catch (e) {
    return new RegExp("$.");
   }
   return q._glob = e, q._src = i, q;
  }, m = {}, i.makeRe = function(e, t) {
   return new o(e, t || {}).makeRe();
  }, o.prototype.makeRe = function w() {
   var e, t, r, n, i;
   if (this.regexp || !1 === this.regexp) return this.regexp;
   if (!(e = this.set).length) return this.regexp = !1, this.regexp;
   t = this.options, r = t.noglobstar ? h : t.dot ? "(?:(?!(?:\\/|^)(?:\\.{1,2})($|\\/)).)*?" : "(?:(?!(?:\\/|^)\\.).)*?", 
   n = t.nocase ? "i" : "", i = "^(?:" + (i = e.map((function(e) {
    return e.map((function(e) {
     return e === c ? r : "string" == typeof e ? function t(e) {
      return e.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
     }(e) : e._src;
    })).join("\\/");
   })).join("|")) + ")$", this.negate && (i = "^(?!" + i + ").*$");
   try {
    this.regexp = new RegExp(i, n);
   } catch (e) {
    this.regexp = !1;
   }
   return this.regexp;
  }, i.match = function(e, t, r) {
   var n = new o(t, r = r || {});
   return e = e.filter((function(e) {
    return n.match(e);
   })), n.options.nonull && !e.length && e.push(t), e;
  }, o.prototype.match = function k(e, t) {
   var r, n, i, o, s, c;
   if (this.debug("match", e, this.pattern), this.comment) return !1;
   if (this.empty) return "" === e;
   if ("/" === e && t) return !0;
   for (r = this.options, "/" !== a.sep && (e = e.split(a.sep).join("/")), e = e.split(p), 
   this.debug(this.pattern, "split", e), n = this.set, this.debug(this.pattern, "set", n), 
   o = e.length - 1; o >= 0 && !(i = e[o]); o--) ;
   for (o = 0; o < n.length; o++) if (s = n[o], c = e, r.matchBase && 1 === s.length && (c = [ i ]), 
   this.matchOne(c, s, t)) return !!r.flipNegate || !this.negate;
   return !r.flipNegate && this.negate;
  }, o.prototype.matchOne = function(e, t, r) {
   var n, i, o, s, a, l, u, f, h, d, p = this.options;
   for (this.debug("matchOne", {
    this: this,
    file: e,
    pattern: t
   }), this.debug("matchOne", e.length, t.length), n = 0, i = 0, o = e.length, s = t.length; n < o && i < s; n++, 
   i++) {
    if (this.debug("matchOne loop"), a = t[i], l = e[n], this.debug(t, a, l), !1 === a) return !1;
    if (a === c) {
     if (this.debug("GLOBSTAR", [ t, a, l ]), u = n, (f = i + 1) === s) {
      for (this.debug("** at the end"); n < o; n++) if ("." === e[n] || ".." === e[n] || !p.dot && "." === e[n].charAt(0)) return !1;
      return !0;
     }
     for (;u < o; ) {
      if (h = e[u], this.debug("\nglobstar while", e, u, t, f, h), this.matchOne(e.slice(u), t.slice(f), r)) return this.debug("globstar found match!", u, o, h), 
      !0;
      if ("." === h || ".." === h || !p.dot && "." === h.charAt(0)) {
       this.debug("dot detected!", e, u, t, f);
       break;
      }
      this.debug("globstar swallow a segment, and continue"), u++;
     }
     return !(!r || (this.debug("\n>>> no match, partial?", e, u, t, f), u !== o));
    }
    if ("string" == typeof a ? (d = p.nocase ? l.toLowerCase() === a.toLowerCase() : l === a, 
    this.debug("string match", a, l, d)) : (d = l.match(a), this.debug("pattern match", a, l, d)), 
    !d) return !1;
   }
   if (n === o && i === s) return !0;
   if (n === o) return r;
   if (i === s) return n === o - 1 && "" === e[n];
   throw new Error("wtf?");
  };
 }, function(e, t, r) {
  function n(e) {
   var t = function() {
    return t.called ? t.value : (t.called = !0, t.value = e.apply(this, arguments));
   };
   return t.called = !1, t;
  }
  function i(e) {
   var t = function() {
    if (t.called) throw new Error(t.onceError);
    return t.called = !0, t.value = e.apply(this, arguments);
   }, r = e.name || "Function wrapped with `once`";
   return t.onceError = r + " shouldn't be called more than once", t.called = !1, t;
  }
  var o = r(123);
  e.exports = o(n), e.exports.strict = o(i), n.proto = n((function() {
   Object.defineProperty(Function.prototype, "once", {
    value: function() {
     return n(this);
    },
    configurable: !0
   }), Object.defineProperty(Function.prototype, "onceStrict", {
    value: function() {
     return i(this);
    },
    configurable: !0
   });
  }));
 }, , function(e, t) {
  e.exports = require$$8__default.default;
 }, , , , function(e, t) {
  e.exports = function(e) {
   if (null == e) throw TypeError("Can't call method on  " + e);
   return e;
  };
 }, function(e, t, r) {
  var n = r(34), i = r(11).document, o = n(i) && n(i.createElement);
  e.exports = function(e) {
   return o ? i.createElement(e) : {};
  };
 }, function(e, t) {
  e.exports = !0;
 }, function(e, t, r) {
  function n(e) {
   var t, r;
   this.promise = new e((function(e, n) {
    if (void 0 !== t || void 0 !== r) throw TypeError("Bad Promise constructor");
    t = e, r = n;
   })), this.resolve = i(t), this.reject = i(r);
  }
  var i = r(46);
  e.exports.f = function(e) {
   return new n(e);
  };
 }, function(e, t, r) {
  var n = r(50).f, i = r(49), o = r(13)("toStringTag");
  e.exports = function(e, t, r) {
   e && !i(e = r ? e : e.prototype, o) && n(e, o, {
    configurable: !0,
    value: t
   });
  };
 }, function(e, t, r) {
  var n = r(107)("keys"), i = r(111);
  e.exports = function(e) {
   return n[e] || (n[e] = i(e));
  };
 }, function(e, t) {
  var r = Math.ceil, n = Math.floor;
  e.exports = function(e) {
   return isNaN(e = +e) ? 0 : (e > 0 ? n : r)(e);
  };
 }, function(e, t, r) {
  var n = r(131), i = r(67);
  e.exports = function(e) {
   return n(i(e));
  };
 }, function(e, t, r) {
  function n(e, t, r) {
   if ("function" == typeof t && (r = t, t = {}), t || (t = {}), t.sync) {
    if (r) throw new TypeError("callback provided to sync glob");
    return d(e, t);
   }
   return new i(e, t, r);
  }
  function i(e, t, r) {
   function n() {
    --s._processing, s._processing <= 0 && (a ? process.nextTick((function() {
     s._finish();
    })) : s._finish());
   }
   var o, s, a, c;
   if ("function" == typeof t && (r = t, t = null), t && t.sync) {
    if (r) throw new TypeError("callback provided to sync glob");
    return new w(e, t);
   }
   if (!(this instanceof i)) return new i(e, t, r);
   if (m(this, e, t), this._didRealPath = !1, o = this.minimatch.set.length, this.matches = new Array(o), 
   "function" == typeof r && (r = _(r), this.on("error", r), this.on("end", (function(e) {
    r(null, e);
   }))), s = this, this._processing = 0, this._emitQueue = [], this._processQueue = [], 
   this.paused = !1, this.noprocess) return this;
   if (0 === o) return n();
   for (a = !0, c = 0; c < o; c++) this._process(this.minimatch.set[c], c, !1, n);
   a = !1;
  }
  var o, s, a, c, l, u, f, h, d, p, m, g, y, b, v, _, w;
  e.exports = n, o = r(3), s = r(114), a = r(60), c = r(42), l = r(54).EventEmitter, 
  u = r(0), f = r(22), h = r(76), d = r(218), p = r(115), m = p.setopts, g = p.ownProp, 
  y = r(223), r(2), b = p.childrenIgnored, v = p.isIgnored, _ = r(61), n.sync = d, 
  w = n.GlobSync = d.GlobSync, n.glob = n, n.hasMagic = function(e, t) {
   var r, n, o = function s(e, t) {
    var r, n;
    if (null === t || "object" != typeof t) return e;
    for (n = (r = Object.keys(t)).length; n--; ) e[r[n]] = t[r[n]];
    return e;
   }({}, t);
   if (o.noprocess = !0, r = new i(e, o).minimatch.set, !e) return !1;
   if (r.length > 1) return !0;
   for (n = 0; n < r[0].length; n++) if ("string" != typeof r[0][n]) return !0;
   return !1;
  }, n.Glob = i, c(i, l), i.prototype._finish = function() {
   if (f(this instanceof i), !this.aborted) {
    if (this.realpath && !this._didRealpath) return this._realpath();
    p.finish(this), this.emit("end", this.found);
   }
  }, i.prototype._realpath = function() {
   function e() {
    0 == --t && r._finish();
   }
   var t, r, n;
   if (!this._didRealpath) {
    if (this._didRealpath = !0, 0 === (t = this.matches.length)) return this._finish();
    for (r = this, n = 0; n < this.matches.length; n++) this._realpathSet(n, e);
   }
  }, i.prototype._realpathSet = function(e, t) {
   var r, n, i, o, a = this.matches[e];
   return a ? (r = Object.keys(a), n = this, 0 === (i = r.length) ? t() : (o = this.matches[e] = Object.create(null), 
   void r.forEach((function(r, a) {
    r = n._makeAbs(r), s.realpath(r, n.realpathCache, (function(s, a) {
     s ? "stat" === s.syscall ? o[r] = !0 : n.emit("error", s) : o[a] = !0, 0 == --i && (n.matches[e] = o, 
     t());
    }));
   })))) : t();
  }, i.prototype._mark = function(e) {
   return p.mark(this, e);
  }, i.prototype._makeAbs = function(e) {
   return p.makeAbs(this, e);
  }, i.prototype.abort = function() {
   this.aborted = !0, this.emit("abort");
  }, i.prototype.pause = function() {
   this.paused || (this.paused = !0, this.emit("pause"));
  }, i.prototype.resume = function() {
   var e, t, r, n, i;
   if (this.paused) {
    if (this.emit("resume"), this.paused = !1, this._emitQueue.length) for (e = this._emitQueue.slice(0), 
    this._emitQueue.length = 0, t = 0; t < e.length; t++) r = e[t], this._emitMatch(r[0], r[1]);
    if (this._processQueue.length) for (n = this._processQueue.slice(0), this._processQueue.length = 0, 
    t = 0; t < n.length; t++) i = n[t], this._processing--, this._process(i[0], i[1], i[2], i[3]);
   }
  }, i.prototype._process = function(e, t, r, n) {
   var o, s, c, l, u;
   if (f(this instanceof i), f("function" == typeof n), !this.aborted) if (this._processing++, 
   this.paused) this._processQueue.push([ e, t, r, n ]); else {
    for (o = 0; "string" == typeof e[o]; ) o++;
    switch (o) {
    case e.length:
     return void this._processSimple(e.join("/"), t, n);

    case 0:
     s = null;
     break;

    default:
     s = e.slice(0, o).join("/");
    }
    if (c = e.slice(o), null === s ? l = "." : h(s) || h(e.join("/")) ? (s && h(s) || (s = "/" + s), 
    l = s) : l = s, u = this._makeAbs(l), b(this, l)) return n();
    c[0] === a.GLOBSTAR ? this._processGlobStar(s, l, u, c, t, r, n) : this._processReaddir(s, l, u, c, t, r, n);
   }
  }, i.prototype._processReaddir = function(e, t, r, n, i, o, s) {
   var a = this;
   this._readdir(r, o, (function(c, l) {
    return a._processReaddir2(e, t, r, n, i, o, l, s);
   }));
  }, i.prototype._processReaddir2 = function(e, t, r, n, i, o, s, a) {
   var c, l, f, h, d, p, m, g;
   if (!s) return a();
   for (c = n[0], l = !!this.minimatch.negate, f = c._glob, h = this.dot || "." === f.charAt(0), 
   d = [], p = 0; p < s.length; p++) ("." !== (m = s[p]).charAt(0) || h) && (l && !e ? !m.match(c) : m.match(c)) && d.push(m);
   if (0 === (g = d.length)) return a();
   if (1 === n.length && !this.mark && !this.stat) {
    for (this.matches[i] || (this.matches[i] = Object.create(null)), p = 0; p < g; p++) m = d[p], 
    e && (m = "/" !== e ? e + "/" + m : e + m), "/" !== m.charAt(0) || this.nomount || (m = u.join(this.root, m)), 
    this._emitMatch(i, m);
    return a();
   }
   for (n.shift(), p = 0; p < g; p++) m = d[p], e && (m = "/" !== e ? e + "/" + m : e + m), 
   this._process([ m ].concat(n), i, o, a);
   a();
  }, i.prototype._emitMatch = function(e, t) {
   var r, n, i;
   this.aborted || v(this, t) || (this.paused ? this._emitQueue.push([ e, t ]) : (r = h(t) ? t : this._makeAbs(t), 
   this.mark && (t = this._mark(t)), this.absolute && (t = r), this.matches[e][t] || this.nodir && ("DIR" === (n = this.cache[r]) || Array.isArray(n)) || (this.matches[e][t] = !0, 
   (i = this.statCache[r]) && this.emit("stat", t, i), this.emit("match", t))));
  }, i.prototype._readdirInGlobStar = function(e, t) {
   var r, n;
   if (!this.aborted) {
    if (this.follow) return this._readdir(e, !1, t);
    r = this, (n = y("lstat\0" + e, (function i(n, o) {
     if (n && "ENOENT" === n.code) return t();
     var s = o && o.isSymbolicLink();
     r.symlinks[e] = s, s || !o || o.isDirectory() ? r._readdir(e, !1, t) : (r.cache[e] = "FILE", 
     t());
    }))) && o.lstat(e, n);
   }
  }, i.prototype._readdir = function(e, t, r) {
   if (!this.aborted && (r = y("readdir\0" + e + "\0" + t, r))) {
    if (t && !g(this.symlinks, e)) return this._readdirInGlobStar(e, r);
    if (g(this.cache, e)) {
     var n = this.cache[e];
     if (!n || "FILE" === n) return r();
     if (Array.isArray(n)) return r(null, n);
    }
    o.readdir(e, function i(e, t, r) {
     return function(n, i) {
      n ? e._readdirError(t, n, r) : e._readdirEntries(t, i, r);
     };
    }(this, e, r));
   }
  }, i.prototype._readdirEntries = function(e, t, r) {
   var n, i;
   if (!this.aborted) {
    if (!this.mark && !this.stat) for (n = 0; n < t.length; n++) i = t[n], i = "/" === e ? e + i : e + "/" + i, 
    this.cache[i] = !0;
    return this.cache[e] = t, r(null, t);
   }
  }, i.prototype._readdirError = function(e, t, r) {
   var n, i;
   if (!this.aborted) {
    switch (t.code) {
    case "ENOTSUP":
    case "ENOTDIR":
     n = this._makeAbs(e), this.cache[n] = "FILE", n === this.cwdAbs && ((i = new Error(t.code + " invalid cwd " + this.cwd)).path = this.cwd, 
     i.code = t.code, this.emit("error", i), this.abort());
     break;

    case "ENOENT":
    case "ELOOP":
    case "ENAMETOOLONG":
    case "UNKNOWN":
     this.cache[this._makeAbs(e)] = !1;
     break;

    default:
     this.cache[this._makeAbs(e)] = !1, this.strict && (this.emit("error", t), this.abort()), 
     this.silent || console.error("glob error", t);
    }
    return r();
   }
  }, i.prototype._processGlobStar = function(e, t, r, n, i, o, s) {
   var a = this;
   this._readdir(r, o, (function(c, l) {
    a._processGlobStar2(e, t, r, n, i, o, l, s);
   }));
  }, i.prototype._processGlobStar2 = function(e, t, r, n, i, o, s, a) {
   var c, l, u, f, h, d, p, m;
   if (!s) return a();
   if (c = n.slice(1), u = (l = e ? [ e ] : []).concat(c), this._process(u, i, !1, a), 
   f = this.symlinks[r], h = s.length, f && o) return a();
   for (d = 0; d < h; d++) ("." !== s[d].charAt(0) || this.dot) && (p = l.concat(s[d], c), 
   this._process(p, i, !0, a), m = l.concat(s[d], n), this._process(m, i, !0, a));
   a();
  }, i.prototype._processSimple = function(e, t, r) {
   var n = this;
   this._stat(e, (function(i, o) {
    n._processSimple2(e, t, i, o, r);
   }));
  }, i.prototype._processSimple2 = function(e, t, r, n, i) {
   if (this.matches[t] || (this.matches[t] = Object.create(null)), !n) return i();
   if (e && h(e) && !this.nomount) {
    var o = /[\/\\]$/.test(e);
    "/" === e.charAt(0) ? e = u.join(this.root, e) : (e = u.resolve(this.root, e), o && (e += "/"));
   }
   "win32" === process.platform && (e = e.replace(/\\/g, "/")), this._emitMatch(t, e), 
   i();
  }, i.prototype._stat = function(e, t) {
   var r, n, i, s, a, c = this._makeAbs(e), l = "/" === e.slice(-1);
   if (e.length > this.maxLength) return t();
   if (!this.stat && g(this.cache, c)) {
    if (r = this.cache[c], Array.isArray(r) && (r = "DIR"), !l || "DIR" === r) return t(null, r);
    if (l && "FILE" === r) return t();
   }
   if (void 0 !== (n = this.statCache[c])) return !1 === n ? t(null, n) : (i = n.isDirectory() ? "DIR" : "FILE", 
   l && "FILE" === i ? t() : t(null, i, n));
   s = this, (a = y("stat\0" + c, (function u(r, n) {
    if (n && n.isSymbolicLink()) return o.stat(c, (function(r, i) {
     r ? s._stat2(e, c, null, n, t) : s._stat2(e, c, r, i, t);
    }));
    s._stat2(e, c, r, n, t);
   }))) && o.lstat(c, a);
  }, i.prototype._stat2 = function(e, t, r, n, i) {
   var o, s;
   return !r || "ENOENT" !== r.code && "ENOTDIR" !== r.code ? (o = "/" === e.slice(-1), 
   this.statCache[t] = n, "/" === t.slice(-1) && n && !n.isDirectory() ? i(null, !1, n) : (s = !0, 
   n && (s = n.isDirectory() ? "DIR" : "FILE"), this.cache[t] = this.cache[t] || s, 
   o && "FILE" === s ? i() : i(null, s, n))) : (this.statCache[t] = !1, i());
  };
 }, function(e, t, r) {
  function n(e) {
   return "/" === e.charAt(0);
  }
  function i(e) {
   var t = /^([a-zA-Z]:|[\\\/]{2}[^\\\/]+[\\\/]+[^\\\/]+)?([\\\/])?([\s\S]*?)$/.exec(e), r = t[1] || "", n = Boolean(r && ":" !== r.charAt(1));
   return Boolean(t[2] || n);
  }
  e.exports = "win32" === process.platform ? i : n, e.exports.posix = n, e.exports.win32 = i;
 }, , , function(e, t) {
  e.exports = require$$9__default.default;
 }, , function(e, t, r) {
  function n() {
   return c = o(r(7));
  }
  function i() {
   return u = r(6);
  }
  function o(e) {
   return e && e.__esModule ? e : {
    default: e
   };
  }
  function s(e, t) {
   const r = new x(e, t);
   return r.next(), r.parse();
  }
  var a, c, l, u, f, h;
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.default = function(e, t = "lockfile") {
   return function i(e) {
    return e.includes(C) && e.includes(S) && e.includes(O);
   }(e = (0, (l || function n() {
    return l = o(r(122));
   }()).default)(e)) ? function a(e, t) {
    const r = function n(e) {
     const t = [ [], [] ], r = e.split(/\r?\n/g);
     let n = !1;
     for (;r.length; ) {
      const e = r.shift();
      if (e.startsWith(C)) {
       for (;r.length; ) {
        const e = r.shift();
        if (e === S) {
         n = !1;
         break;
        }
        n || e.startsWith("|||||||") ? n = !0 : t[0].push(e);
       }
       for (;r.length; ) {
        const e = r.shift();
        if (e.startsWith(O)) break;
        t[1].push(e);
       }
      } else t[0].push(e), t[1].push(e);
     }
     return [ t[0].join("\n"), t[1].join("\n") ];
    }(e);
    try {
     return {
      type: "merge",
      object: Object.assign({}, s(r[0], t), s(r[1], t))
     };
    } catch (e) {
     if (e instanceof SyntaxError) return {
      type: "conflict",
      object: {}
     };
     throw e;
    }
   }(e, t) : {
    type: "success",
    object: s(e, t)
   };
  };
  const d = /^yarn lockfile v(\d+)$/, p = "BOOLEAN", m = "STRING", g = "COLON", y = "NEWLINE", b = "COMMENT", v = "INDENT", _ = "INVALID", w = "NUMBER", k = "COMMA", E = [ p, m, w ];
  class x {
   constructor(e, t = "lockfile") {
    this.comments = [], this.tokens = function* r(e) {
     function t(e, t) {
      return {
       line: n,
       col: i,
       type: e,
       value: t
      };
     }
     let r = !1, n = 1, i = 0;
     for (;e.length; ) {
      let o = 0;
      if ("\n" === e[0] || "\r" === e[0]) o++, "\n" === e[1] && o++, n++, i = 0, yield t(y); else if ("#" === e[0]) {
       o++;
       let r = "";
       for (;"\n" !== e[o]; ) r += e[o], o++;
       yield t(b, r);
      } else if (" " === e[0]) if (r) {
       let r = "";
       for (let t = 0; " " === e[t]; t++) r += e[t];
       if (r.length % 2) throw new TypeError("Invalid number of spaces");
       o = r.length, yield t(v, r.length / 2);
      } else o++; else if ('"' === e[0]) {
       let r = "";
       for (let t = 0; ;t++) {
        const n = e[t];
        if (r += n, t > 0 && '"' === n && ("\\" !== e[t - 1] || "\\" === e[t - 2])) break;
       }
       o = r.length;
       try {
        yield t(m, JSON.parse(r));
       } catch (e) {
        if (!(e instanceof SyntaxError)) throw e;
        yield t(_);
       }
      } else if (/^[0-9]/.test(e)) {
       let r = "";
       for (let t = 0; /^[0-9]$/.test(e[t]); t++) r += e[t];
       o = r.length, yield t(w, +r);
      } else if (/^true/.test(e)) yield t(p, !0), o = 4; else if (/^false/.test(e)) yield t(p, !1), 
      o = 5; else if (":" === e[0]) yield t(g), o++; else if ("," === e[0]) yield t(k), 
      o++; else if (/^[a-zA-Z\/-]/g.test(e)) {
       let r = "";
       for (let t = 0; t < e.length; t++) {
        const n = e[t];
        if (":" === n || " " === n || "\n" === n || "\r" === n || "," === n) break;
        r += n;
       }
       o = r.length, yield t(m, r);
      } else yield t(_);
      o || (yield t(_)), i += o, r = "\n" === e[0] || "\r" === e[0] && "\n" === e[1], 
      e = e.slice(o);
     }
     yield t("EOF");
    }(e), this.fileLoc = t;
   }
   onComment(e) {
    const t = e.value;
    (0, (c || n()).default)("string" == typeof t, "expected token value to be a string");
    const o = t.trim(), s = o.match(d);
    if (s) {
     const e = +s[1];
     if (e > (u || i()).LOCKFILE_VERSION) throw new ((f || function a() {
      return f = r(4);
     }()).MessageError)(`Can't install from a lockfile of version ${e} as you're on an old yarn version that only supports versions up to ${(u || i()).LOCKFILE_VERSION}. Run \`$ yarn self-update\` to upgrade to the latest version.`);
    }
    this.comments.push(o);
   }
   next() {
    const e = this.tokens.next();
    (0, (c || n()).default)(e, "expected a token");
    const t = e.done, r = e.value;
    if (t || !r) throw new Error("No more tokens");
    return r.type === b ? (this.onComment(r), this.next()) : this.token = r;
   }
   unexpected(e = "Unexpected token") {
    throw new SyntaxError(`${e} ${this.token.line}:${this.token.col} in ${this.fileLoc}`);
   }
   expect(e) {
    this.token.type === e ? this.next() : this.unexpected();
   }
   eat(e) {
    return this.token.type === e && (this.next(), !0);
   }
   parse(e = 0) {
    var t, i, s, l, u, f, d, p, b;
    const _ = (0, (h || function w() {
     return h = o(r(20));
    }()).default)();
    for (;;) {
     const h = this.token;
     if (h.type === y) {
      const t = this.next();
      if (!e) continue;
      if (t.type !== v) break;
      if (t.value !== e) break;
      this.next();
     } else if (h.type === v) {
      if (h.value !== e) break;
      this.next();
     } else {
      if ("EOF" === h.type) break;
      if (h.type === m) {
       const r = h.value;
       (0, (c || n()).default)(r, "Expected a key");
       const o = [ r ];
       for (this.next(); this.token.type === k; ) {
        this.next();
        const e = this.token;
        e.type !== m && this.unexpected("Expected string");
        const t = e.value;
        (0, (c || n()).default)(t, "Expected a key"), o.push(t), this.next();
       }
       const a = this.token;
       if (a.type === g) {
        this.next();
        const r = this.parse(e + 1);
        for (t = o, s = 0, t = (i = Array.isArray(t)) ? t : t[Symbol.iterator](); ;) {
         if (i) {
          if (s >= t.length) break;
          l = t[s++];
         } else {
          if ((s = t.next()).done) break;
          l = s.value;
         }
         _[l] = r;
        }
        if (e && this.token.type !== v) break;
       } else if (b = a, E.indexOf(b.type) >= 0) {
        for (u = o, d = 0, u = (f = Array.isArray(u)) ? u : u[Symbol.iterator](); ;) {
         if (f) {
          if (d >= u.length) break;
          p = u[d++];
         } else {
          if ((d = u.next()).done) break;
          p = d.value;
         }
         _[p] = a.value;
        }
        this.next();
       } else this.unexpected("Invalid value type");
      } else this.unexpected(`Unknown token: ${(a || (a = o(r(2)))).default.inspect(h)}`);
     }
    }
    return _;
   }
  }
  const O = ">>>>>>>", S = "=======", C = "<<<<<<<";
 }, , , function(e, t, r) {
  function n() {
   return i = function e(t) {
    return t && t.__esModule ? t : {
     default: t
    };
   }(r(20));
  }
  var i;
  Object.defineProperty(t, "__esModule", {
   value: !0
  });
  const o = r(212)("yarn");
  t.default = class s {
   constructor(e, t = 1 / 0) {
    this.concurrencyQueue = [], this.maxConcurrency = t, this.runningCount = 0, this.warnedStuck = !1, 
    this.alias = e, this.first = !0, this.running = (0, (i || n()).default)(), this.queue = (0, 
    (i || n()).default)(), this.stuckTick = this.stuckTick.bind(this);
   }
   stillActive() {
    this.stuckTimer && clearTimeout(this.stuckTimer), this.stuckTimer = setTimeout(this.stuckTick, 5e3), 
    this.stuckTimer.unref && this.stuckTimer.unref();
   }
   stuckTick() {
    1 === this.runningCount && (this.warnedStuck = !0, o(`The ${JSON.stringify(this.alias)} blocking queue may be stuck. 5 seconds without any activity with 1 worker: ${Object.keys(this.running)[0]}`));
   }
   push(e, t) {
    return this.first ? this.first = !1 : this.stillActive(), new Promise(((r, n) => {
     (this.queue[e] = this.queue[e] || []).push({
      factory: t,
      resolve: r,
      reject: n
     }), this.running[e] || this.shift(e);
    }));
   }
   shift(e) {
    this.running[e] && (delete this.running[e], this.runningCount--, this.stuckTimer && (clearTimeout(this.stuckTimer), 
    this.stuckTimer = null), this.warnedStuck && (this.warnedStuck = !1, o(`${JSON.stringify(this.alias)} blocking queue finally resolved. Nothing to worry about.`)));
    const t = this.queue[e];
    if (!t) return;
    var r = t.shift();
    const n = r.resolve, i = r.reject, s = r.factory;
    t.length || delete this.queue[e];
    const a = () => {
     this.shift(e), this.shiftConcurrencyQueue();
    };
    this.maybePushConcurrencyQueue((() => {
     this.running[e] = !0, this.runningCount++, s().then((function(e) {
      return n(e), a(), null;
     })).catch((function(e) {
      i(e), a();
     }));
    }));
   }
   maybePushConcurrencyQueue(e) {
    this.runningCount < this.maxConcurrency ? e() : this.concurrencyQueue.push(e);
   }
   shiftConcurrencyQueue() {
    if (this.runningCount < this.maxConcurrency) {
     const e = this.concurrencyQueue.shift();
     e && e();
    }
   }
  };
 }, function(e, t) {
  e.exports = function(e) {
   try {
    return !!e();
   } catch (e) {
    return !0;
   }
  };
 }, , , , , , , , , , , , , , , function(e, t, r) {
  var n = r(47), i = r(13)("toStringTag"), o = "Arguments" == n(function() {
   return arguments;
  }());
  e.exports = function(e) {
   var t, r, s;
   return void 0 === e ? "Undefined" : null === e ? "Null" : "string" == typeof (r = function(e, t) {
    try {
     return e[t];
    } catch (e) {}
   }(t = Object(e), i)) ? r : o ? n(t) : "Object" == (s = n(t)) && "function" == typeof t.callee ? "Arguments" : s;
  };
 }, function(e, t) {
  e.exports = "constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf".split(",");
 }, function(e, t, r) {
  var n = r(11).document;
  e.exports = n && n.documentElement;
 }, function(e, t, r) {
  var n = r(69), i = r(41), o = r(197), s = r(31), a = r(35), c = r(188), l = r(71), u = r(194), f = r(13)("iterator"), h = !([].keys && "next" in [].keys()), d = "keys", p = "values", m = function() {
   return this;
  };
  e.exports = function(e, t, r, g, y, b, v) {
   var _, w, k, E, x, O, S, C, A, j, T, P;
   if (c(r, t, g), _ = function(e) {
    if (!h && e in x) return x[e];
    switch (e) {
    case d:
     return function t() {
      return new r(this, e);
     };

    case p:
     return function t() {
      return new r(this, e);
     };
    }
    return function t() {
     return new r(this, e);
    };
   }, w = t + " Iterator", k = y == p, E = !1, x = e.prototype, S = (O = x[f] || x["@@iterator"] || y && x[y]) || _(y), 
   C = y ? k ? _("entries") : S : void 0, (A = "Array" == t && x.entries || O) && (P = u(A.call(new e))) !== Object.prototype && P.next && (l(P, w, !0), 
   n || "function" == typeof P[f] || s(P, f, m)), k && O && O.name !== p && (E = !0, 
   S = function e() {
    return O.call(this);
   }), n && !v || !h && !E && x[f] || s(x, f, S), a[t] = S, a[w] = m, y) if (j = {
    values: k ? S : _(p),
    keys: b ? S : _(d),
    entries: C
   }, v) for (T in j) T in x || o(x, T, j[T]); else i(i.P + i.F * (h || E), t, j);
   return j;
  };
 }, function(e, t) {
  e.exports = function(e) {
   try {
    return {
     e: !1,
     v: e()
    };
   } catch (e) {
    return {
     e: !0,
     v: e
    };
   }
  };
 }, function(e, t, r) {
  var n = r(27), i = r(34), o = r(70);
  e.exports = function(e, t) {
   var r;
   return n(e), i(t) && t.constructor === e ? t : ((0, (r = o.f(e)).resolve)(t), r.promise);
  };
 }, function(e, t) {
  e.exports = function(e, t) {
   return {
    enumerable: !(1 & e),
    configurable: !(2 & e),
    writable: !(4 & e),
    value: t
   };
  };
 }, function(e, t, r) {
  var n = r(23), i = r(11), o = "__core-js_shared__", s = i[o] || (i[o] = {});
  (e.exports = function(e, t) {
   return s[e] || (s[e] = void 0 !== t ? t : {});
  })("versions", []).push({
   version: n.version,
   mode: r(69) ? "pure" : "global",
   copyright: "© 2018 Denis Pushkarev (zloirock.ru)"
  });
 }, function(e, t, r) {
  var n = r(27), i = r(46), o = r(13)("species");
  e.exports = function(e, t) {
   var r, s = n(e).constructor;
   return void 0 === s || null == (r = n(s)[o]) ? t : i(r);
  };
 }, function(e, t, r) {
  var n, i, o, s = r(48), a = r(185), c = r(102), l = r(68), u = r(11), f = u.process, h = u.setImmediate, d = u.clearImmediate, p = u.MessageChannel, m = u.Dispatch, g = 0, y = {}, b = "onreadystatechange", v = function() {
   var e, t = +this;
   y.hasOwnProperty(t) && (e = y[t], delete y[t], e());
  }, _ = function(e) {
   v.call(e.data);
  };
  h && d || (h = function e(t) {
   for (var r = [], i = 1; arguments.length > i; ) r.push(arguments[i++]);
   return y[++g] = function() {
    a("function" == typeof t ? t : Function(t), r);
   }, n(g), g;
  }, d = function e(t) {
   delete y[t];
  }, "process" == r(47)(f) ? n = function(e) {
   f.nextTick(s(v, e, 1));
  } : m && m.now ? n = function(e) {
   m.now(s(v, e, 1));
  } : p ? (o = (i = new p).port2, i.port1.onmessage = _, n = s(o.postMessage, o, 1)) : u.addEventListener && "function" == typeof postMessage && !u.importScripts ? (n = function(e) {
   u.postMessage(e + "", "*");
  }, u.addEventListener("message", _, !1)) : n = b in l("script") ? function(e) {
   c.appendChild(l("script")).onreadystatechange = function() {
    c.removeChild(this), v.call(e);
   };
  } : function(e) {
   setTimeout(s(v, e, 1), 0);
  }), e.exports = {
   set: h,
   clear: d
  };
 }, function(e, t, r) {
  var n = r(73), i = Math.min;
  e.exports = function(e) {
   return e > 0 ? i(n(e), 9007199254740991) : 0;
  };
 }, function(e, t) {
  var r = 0, n = Math.random();
  e.exports = function(e) {
   return "Symbol(".concat(void 0 === e ? "" : e, ")_", (++r + n).toString(36));
  };
 }, function(e, t, r) {
  function n(e) {
   function r() {
    var e, i, o, s, a, c;
    if (r.enabled) {
     for (e = r, o = (i = +new Date) - (n || i), e.diff = o, e.prev = n, e.curr = i, 
     n = i, s = new Array(arguments.length), a = 0; a < s.length; a++) s[a] = arguments[a];
     s[0] = t.coerce(s[0]), "string" != typeof s[0] && s.unshift("%O"), c = 0, s[0] = s[0].replace(/%([a-zA-Z%])/g, (function(r, n) {
      var i, o;
      return "%%" === r || (c++, "function" == typeof (i = t.formatters[n]) && (o = s[c], 
      r = i.call(e, o), s.splice(c, 1), c--)), r;
     })), t.formatArgs.call(e, s), (r.log || t.log || console.log.bind(console)).apply(e, s);
    }
   }
   var n;
   return r.namespace = e, r.enabled = t.enabled(e), r.useColors = t.useColors(), r.color = function o(e) {
    var r, n = 0;
    for (r in e) n = (n << 5) - n + e.charCodeAt(r), n |= 0;
    return t.colors[Math.abs(n) % t.colors.length];
   }(e), r.destroy = i, "function" == typeof t.init && t.init(r), t.instances.push(r), 
   r;
  }
  function i() {
   var e = t.instances.indexOf(this);
   return -1 !== e && (t.instances.splice(e, 1), !0);
  }
  (t = e.exports = n.debug = n.default = n).coerce = function o(e) {
   return e instanceof Error ? e.stack || e.message : e;
  }, t.disable = function s() {
   t.enable("");
  }, t.enable = function a(e) {
   var r, n, i, o;
   for (t.save(e), t.names = [], t.skips = [], i = (n = ("string" == typeof e ? e : "").split(/[\s,]+/)).length, 
   r = 0; r < i; r++) n[r] && ("-" === (e = n[r].replace(/\*/g, ".*?"))[0] ? t.skips.push(new RegExp("^" + e.substr(1) + "$")) : t.names.push(new RegExp("^" + e + "$")));
   for (r = 0; r < t.instances.length; r++) (o = t.instances[r]).enabled = t.enabled(o.namespace);
  }, t.enabled = function c(e) {
   if ("*" === e[e.length - 1]) return !0;
   var r, n;
   for (r = 0, n = t.skips.length; r < n; r++) if (t.skips[r].test(e)) return !1;
   for (r = 0, n = t.names.length; r < n; r++) if (t.names[r].test(e)) return !0;
   return !1;
  }, t.humanize = r(229), t.instances = [], t.names = [], t.skips = [], t.formatters = {};
 }, , function(e, t, r) {
  function n(e) {
   return e && "realpath" === e.syscall && ("ELOOP" === e.code || "ENOMEM" === e.code || "ENAMETOOLONG" === e.code);
  }
  function i(e, t, r) {
   if (u) return a(e, t, r);
   "function" == typeof t && (r = t, t = null), a(e, t, (function(i, o) {
    n(i) ? f.realpath(e, t, r) : r(i, o);
   }));
  }
  function o(e, t) {
   if (u) return c(e, t);
   try {
    return c(e, t);
   } catch (r) {
    if (n(r)) return f.realpathSync(e, t);
    throw r;
   }
  }
  var s, a, c, l, u, f;
  e.exports = i, i.realpath = i, i.sync = o, i.realpathSync = o, i.monkeypatch = function h() {
   s.realpath = i, s.realpathSync = o;
  }, i.unmonkeypatch = function d() {
   s.realpath = a, s.realpathSync = c;
  }, s = r(3), a = s.realpath, c = s.realpathSync, l = process.version, u = /^v[0-5]\./.test(l), 
  f = r(217);
 }, function(e, t, r) {
  function n(e, t) {
   return Object.prototype.hasOwnProperty.call(e, t);
  }
  function i(e, t) {
   return e.toLowerCase().localeCompare(t.toLowerCase());
  }
  function o(e, t) {
   return e.localeCompare(t);
  }
  function s(e) {
   var t, r = null;
   return "/**" === e.slice(-3) && (t = e.replace(/(\/\*\*)+$/, ""), r = new h(t, {
    dot: !0
   })), {
    matcher: new h(e, {
     dot: !0
    }),
    gmatcher: r
   };
  }
  function a(e, t) {
   var r = t;
   return r = "/" === t.charAt(0) ? l.join(e.root, t) : f(t) || "" === t ? t : e.changedCwd ? l.resolve(e.cwd, t) : l.resolve(t), 
   "win32" === process.platform && (r = r.replace(/\\/g, "/")), r;
  }
  function c(e, t) {
   return !!e.ignore.length && e.ignore.some((function(e) {
    return e.matcher.match(t) || !(!e.gmatcher || !e.gmatcher.match(t));
   }));
  }
  var l, u, f, h;
  t.alphasort = o, t.alphasorti = i, t.setopts = function d(e, t, r) {
   if (r || (r = {}), r.matchBase && -1 === t.indexOf("/")) {
    if (r.noglobstar) throw new Error("base matching requires globstar");
    t = "**/" + t;
   }
   e.silent = !!r.silent, e.pattern = t, e.strict = !1 !== r.strict, e.realpath = !!r.realpath, 
   e.realpathCache = r.realpathCache || Object.create(null), e.follow = !!r.follow, 
   e.dot = !!r.dot, e.mark = !!r.mark, e.nodir = !!r.nodir, e.nodir && (e.mark = !0), 
   e.sync = !!r.sync, e.nounique = !!r.nounique, e.nonull = !!r.nonull, e.nosort = !!r.nosort, 
   e.nocase = !!r.nocase, e.stat = !!r.stat, e.noprocess = !!r.noprocess, e.absolute = !!r.absolute, 
   e.maxLength = r.maxLength || 1 / 0, e.cache = r.cache || Object.create(null), e.statCache = r.statCache || Object.create(null), 
   e.symlinks = r.symlinks || Object.create(null), function i(e, t) {
    e.ignore = t.ignore || [], Array.isArray(e.ignore) || (e.ignore = [ e.ignore ]), 
    e.ignore.length && (e.ignore = e.ignore.map(s));
   }(e, r), e.changedCwd = !1;
   var o = process.cwd();
   n(r, "cwd") ? (e.cwd = l.resolve(r.cwd), e.changedCwd = e.cwd !== o) : e.cwd = o, 
   e.root = r.root || l.resolve(e.cwd, "/"), e.root = l.resolve(e.root), "win32" === process.platform && (e.root = e.root.replace(/\\/g, "/")), 
   e.cwdAbs = f(e.cwd) ? e.cwd : a(e, e.cwd), "win32" === process.platform && (e.cwdAbs = e.cwdAbs.replace(/\\/g, "/")), 
   e.nomount = !!r.nomount, r.nonegate = !0, r.nocomment = !0, e.minimatch = new h(t, r), 
   e.options = e.minimatch.options;
  }, t.ownProp = n, t.makeAbs = a, t.finish = function p(e) {
   var t, r, n, s, l, u = e.nounique, f = u ? [] : Object.create(null);
   for (t = 0, r = e.matches.length; t < r; t++) (n = e.matches[t]) && 0 !== Object.keys(n).length ? (l = Object.keys(n), 
   u ? f.push.apply(f, l) : l.forEach((function(e) {
    f[e] = !0;
   }))) : e.nonull && (s = e.minimatch.globSet[t], u ? f.push(s) : f[s] = !0);
   if (u || (f = Object.keys(f)), e.nosort || (f = f.sort(e.nocase ? i : o)), e.mark) {
    for (t = 0; t < f.length; t++) f[t] = e._mark(f[t]);
    e.nodir && (f = f.filter((function(t) {
     var r = !/\/$/.test(t), n = e.cache[t] || e.cache[a(e, t)];
     return r && n && (r = "DIR" !== n && !Array.isArray(n)), r;
    })));
   }
   e.ignore.length && (f = f.filter((function(t) {
    return !c(e, t);
   }))), e.found = f;
  }, t.mark = function m(e, t) {
   var r, n, i, o = a(e, t), s = e.cache[o], c = t;
   return s && (r = "DIR" === s || Array.isArray(s), n = "/" === t.slice(-1), r && !n ? c += "/" : !r && n && (c = c.slice(0, -1)), 
   c !== t && (i = a(e, c), e.statCache[i] = e.statCache[o], e.cache[i] = e.cache[o])), 
   c;
  }, t.isIgnored = c, t.childrenIgnored = function g(e, t) {
   return !!e.ignore.length && e.ignore.some((function(e) {
    return !(!e.gmatcher || !e.gmatcher.match(t));
   }));
  }, l = r(0), u = r(60), f = r(76), h = u.Minimatch;
 }, function(e, t, r) {
  function n(e, t, r, a) {
   var c, l, u;
   "function" == typeof t ? (r = t, t = {}) : t && "object" == typeof t || (t = {
    mode: t
   }), c = t.mode, l = t.fs || o, void 0 === c && (c = s & ~process.umask()), a || (a = null), 
   u = r || function() {}, e = i.resolve(e), l.mkdir(e, c, (function(r) {
    if (!r) return u(null, a = a || e);
    switch (r.code) {
    case "ENOENT":
     n(i.dirname(e), t, (function(r, i) {
      r ? u(r, i) : n(e, t, u, i);
     }));
     break;

    default:
     l.stat(e, (function(e, t) {
      e || !t.isDirectory() ? u(r, a) : u(null, a);
     }));
    }
   }));
  }
  var i = r(0), o = r(3), s = parseInt("0777", 8);
  e.exports = n.mkdirp = n.mkdirP = n, n.sync = function e(t, r, n) {
   var a, c, l;
   r && "object" == typeof r || (r = {
    mode: r
   }), a = r.mode, c = r.fs || o, void 0 === a && (a = s & ~process.umask()), n || (n = null), 
   t = i.resolve(t);
   try {
    c.mkdirSync(t, a), n = n || t;
   } catch (o) {
    switch (o.code) {
    case "ENOENT":
     n = e(i.dirname(t), r, n), e(t, r, n);
     break;

    default:
     try {
      l = c.statSync(t);
     } catch (e) {
      throw o;
     }
     if (!l.isDirectory()) throw o;
    }
   }
   return n;
  };
 }, , , , , , function(e, t, r) {
  e.exports = e => {
   if ("string" != typeof e) throw new TypeError("Expected a string, got " + typeof e);
   return 65279 === e.charCodeAt(0) ? e.slice(1) : e;
  };
 }, function(e, t) {
  e.exports = function e(t, r) {
   function n() {
    var e, r, n, i = new Array(arguments.length);
    for (e = 0; e < i.length; e++) i[e] = arguments[e];
    return r = t.apply(this, i), n = i[i.length - 1], "function" == typeof r && r !== n && Object.keys(n).forEach((function(e) {
     r[e] = n[e];
    })), r;
   }
   if (t && r) return e(t)(r);
   if ("function" != typeof t) throw new TypeError("need wrapper function");
   return Object.keys(t).forEach((function(e) {
    n[e] = t[e];
   })), n;
  };
 }, , , , , , , , function(e, t, r) {
  var n = r(47);
  e.exports = Object("z").propertyIsEnumerable(0) ? Object : function(e) {
   return "String" == n(e) ? e.split("") : Object(e);
  };
 }, function(e, t, r) {
  var n = r(195), i = r(101);
  e.exports = Object.keys || function e(t) {
   return n(t, i);
  };
 }, function(e, t, r) {
  var n = r(67);
  e.exports = function(e) {
   return Object(n(e));
  };
 }, , , , , , , , , , , , function(e, t) {
  e.exports = {
   name: "yarn",
   installationMethod: "unknown",
   version: "1.10.0-0",
   license: "BSD-2-Clause",
   preferGlobal: !0,
   description: "📦🐈 Fast, reliable, and secure dependency management.",
   dependencies: {
    "@zkochan/cmd-shim": "^2.2.4",
    "babel-runtime": "^6.26.0",
    bytes: "^3.0.0",
    camelcase: "^4.0.0",
    chalk: "^2.1.0",
    commander: "^2.9.0",
    death: "^1.0.0",
    debug: "^3.0.0",
    "deep-equal": "^1.0.1",
    "detect-indent": "^5.0.0",
    dnscache: "^1.0.1",
    glob: "^7.1.1",
    "gunzip-maybe": "^1.4.0",
    "hash-for-dep": "^1.2.3",
    "imports-loader": "^0.8.0",
    ini: "^1.3.4",
    inquirer: "^3.0.1",
    invariant: "^2.2.0",
    "is-builtin-module": "^2.0.0",
    "is-ci": "^1.0.10",
    "is-webpack-bundle": "^1.0.0",
    leven: "^2.0.0",
    "loud-rejection": "^1.2.0",
    micromatch: "^2.3.11",
    mkdirp: "^0.5.1",
    "node-emoji": "^1.6.1",
    "normalize-url": "^2.0.0",
    "npm-logical-tree": "^1.2.1",
    "object-path": "^0.11.2",
    "proper-lockfile": "^2.0.0",
    puka: "^1.0.0",
    read: "^1.0.7",
    request: "^2.87.0",
    "request-capture-har": "^1.2.2",
    rimraf: "^2.5.0",
    semver: "^5.1.0",
    ssri: "^5.3.0",
    "strip-ansi": "^4.0.0",
    "strip-bom": "^3.0.0",
    "tar-fs": "^1.16.0",
    "tar-stream": "^1.6.1",
    uuid: "^3.0.1",
    "v8-compile-cache": "^2.0.0",
    "validate-npm-package-license": "^3.0.3",
    yn: "^2.0.0"
   },
   devDependencies: {
    "babel-core": "^6.26.0",
    "babel-eslint": "^7.2.3",
    "babel-loader": "^6.2.5",
    "babel-plugin-array-includes": "^2.0.3",
    "babel-plugin-transform-builtin-extend": "^1.1.2",
    "babel-plugin-transform-inline-imports-commonjs": "^1.0.0",
    "babel-plugin-transform-runtime": "^6.4.3",
    "babel-preset-env": "^1.6.0",
    "babel-preset-flow": "^6.23.0",
    "babel-preset-stage-0": "^6.0.0",
    babylon: "^6.5.0",
    commitizen: "^2.9.6",
    "cz-conventional-changelog": "^2.0.0",
    eslint: "^4.3.0",
    "eslint-config-fb-strict": "^22.0.0",
    "eslint-plugin-babel": "^5.0.0",
    "eslint-plugin-flowtype": "^2.35.0",
    "eslint-plugin-jasmine": "^2.6.2",
    "eslint-plugin-jest": "^21.0.0",
    "eslint-plugin-jsx-a11y": "^6.0.2",
    "eslint-plugin-prefer-object-spread": "^1.2.1",
    "eslint-plugin-prettier": "^2.1.2",
    "eslint-plugin-react": "^7.1.0",
    "eslint-plugin-relay": "^0.0.24",
    "eslint-plugin-yarn-internal": "file:scripts/eslint-rules",
    execa: "^0.10.0",
    "flow-bin": "^0.66.0",
    "git-release-notes": "^3.0.0",
    gulp: "^3.9.0",
    "gulp-babel": "^7.0.0",
    "gulp-if": "^2.0.1",
    "gulp-newer": "^1.0.0",
    "gulp-plumber": "^1.0.1",
    "gulp-sourcemaps": "^2.2.0",
    "gulp-util": "^3.0.7",
    "gulp-watch": "^5.0.0",
    jest: "^22.4.4",
    jsinspect: "^0.12.6",
    minimatch: "^3.0.4",
    "mock-stdin": "^0.3.0",
    prettier: "^1.5.2",
    temp: "^0.8.3",
    webpack: "^2.1.0-beta.25",
    yargs: "^6.3.0"
   },
   resolutions: {
    sshpk: "^1.14.2"
   },
   engines: {
    node: ">=4.0.0"
   },
   repository: "yarnpkg/yarn",
   bin: {
    yarn: "./bin/yarn.js",
    yarnpkg: "./bin/yarn.js"
   },
   scripts: {
    build: "gulp build",
    "build-bundle": "node ./scripts/build-webpack.js",
    "build-chocolatey": "powershell ./scripts/build-chocolatey.ps1",
    "build-deb": "./scripts/build-deb.sh",
    "build-dist": "bash ./scripts/build-dist.sh",
    "build-win-installer": "scripts\\build-windows-installer.bat",
    changelog: "git-release-notes $(git describe --tags --abbrev=0 $(git describe --tags --abbrev=0)^)..$(git describe --tags --abbrev=0) scripts/changelog.md",
    "dupe-check": "yarn jsinspect ./src",
    lint: "eslint . && flow check",
    "pkg-tests": "yarn --cwd packages/pkg-tests jest yarn.test.js",
    prettier: "eslint src __tests__ --fix",
    "release-branch": "./scripts/release-branch.sh",
    test: "yarn lint && yarn test-only",
    "test-only": "node --max_old_space_size=4096 node_modules/jest/bin/jest.js --verbose",
    "test-only-debug": "node --inspect-brk --max_old_space_size=4096 node_modules/jest/bin/jest.js --runInBand --verbose",
    "test-coverage": "node --max_old_space_size=4096 node_modules/jest/bin/jest.js --coverage --verbose",
    watch: "gulp watch",
    commit: "git-cz"
   },
   jest: {
    collectCoverageFrom: [ "src/**/*.js" ],
    testEnvironment: "node",
    modulePathIgnorePatterns: [ "__tests__/fixtures/", "packages/pkg-tests/pkg-tests-fixtures", "dist/" ],
    testPathIgnorePatterns: [ "__tests__/(fixtures|__mocks__)/", "updates/", "_(temp|mock|install|init|helpers).js$", "packages/pkg-tests" ]
   },
   config: {
    commitizen: {
     path: "./node_modules/cz-conventional-changelog"
    }
   }
  };
 }, , , , , function(e, t, r) {
  function n() {
   return a = r(12);
  }
  function i(e) {
   return "boolean" == typeof e || "number" == typeof e || function t(e) {
    return 0 === e.indexOf("true") || 0 === e.indexOf("false") || /[:\s\n\\",\[\]]/g.test(e) || /^[0-9]/g.test(e) || !/^[a-zA-Z]/g.test(e);
   }(e) ? JSON.stringify(e) : e;
  }
  function o(e, t) {
   return h[e] || h[t] ? (h[e] || 100) > (h[t] || 100) ? 1 : -1 : (0, (a || n()).sortAlpha)(e, t);
  }
  function s(e, t) {
   if ("object" != typeof e) throw new TypeError;
   const r = t.indent, c = [], l = Object.keys(e).sort(o);
   let u = [];
   for (let o = 0; o < l.length; o++) {
    const f = l[o], h = e[f];
    if (null == h || u.indexOf(f) >= 0) continue;
    const d = [ f ];
    if ("object" == typeof h) for (let t = o + 1; t < l.length; t++) {
     const r = l[t];
     h === e[r] && d.push(r);
    }
    const p = d.sort((a || n()).sortAlpha).map(i).join(", ");
    if ("string" == typeof h || "boolean" == typeof h || "number" == typeof h) c.push(`${p} ${i(h)}`); else {
     if ("object" != typeof h) throw new TypeError;
     c.push(`${p}:\n${s(h, {
      indent: r + "  "
     })}` + (t.topLevel ? "\n" : ""));
    }
    u = u.concat(d);
   }
   return r + c.join(`\n${r}`);
  }
  var a, c, l;
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.default = function u(e, t, n) {
   const i = s(e, {
    indent: "",
    topLevel: !0
   });
   if (t) return i;
   const o = [];
   return o.push("# THIS IS AN AUTOGENERATED FILE. DO NOT EDIT THIS FILE DIRECTLY."), 
   o.push(`# yarn lockfile v${(c || function a() {
    return c = r(6);
   }()).LOCKFILE_VERSION}`), n && (o.push(`# yarn v${(l || function u() {
    return l = r(145);
   }()).version}`), o.push(`# node ${f}`)), o.push("\n"), o.push(i), o.join("\n");
  };
  const f = process.version, h = {
   name: 1,
   version: 2,
   uid: 3,
   resolved: 4,
   integrity: 5,
   registry: 6,
   dependencies: 7
  };
 }, , , , , , , , , , , , , , function(e, t, r) {
  function n() {
   return a = s(r(1));
  }
  function i() {
   return c = s(r(3));
  }
  function o() {
   return l = r(40);
  }
  function s(e) {
   return e && e.__esModule ? e : {
    default: e
   };
  }
  var a, c, l, u, f, h;
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.fileDatesEqual = t.copyFile = t.unlink = void 0;
  let d, p = (u = (0, (a || n()).default)((function*(e, t, r) {
   const n = void 0 === e;
   let i = e || -1;
   if (void 0 === d) {
    const e = yield y(t);
    d = x(e.mtime, r.mtime);
   }
   if (!d) {
    if (n) try {
     i = yield b(t, "a", r.mode);
    } catch (e) {
     try {
      i = yield b(t, "r", r.mode);
     } catch (e) {
      return;
     }
    }
    try {
     i && (yield v(i, r.atime, r.mtime));
    } catch (e) {} finally {
     n && i && (yield g(i));
    }
   }
  })), function e(t, r, n) {
   return u.apply(this, arguments);
  });
  const m = (0, (l || o()).promisify)((c || i()).default.readFile), g = (0, (l || o()).promisify)((c || i()).default.close), y = (0, 
  (l || o()).promisify)((c || i()).default.lstat), b = (0, (l || o()).promisify)((c || i()).default.open), v = (0, 
  (l || o()).promisify)((c || i()).default.futimes), _ = (0, (l || o()).promisify)((c || i()).default.write), w = t.unlink = (0, 
  (l || o()).promisify)(r(233));
  t.copyFile = (f = (0, (a || n()).default)((function*(e, t) {
   try {
    yield w(e.dest), yield k(e.src, e.dest, 0, e);
   } finally {
    t && t();
   }
  })), function e(t, r) {
   return f.apply(this, arguments);
  });
  const k = (e, t, r, n) => (c || i()).default.copyFile ? new Promise(((o, s) => (c || i()).default.copyFile(e, t, r, (e => {
   e ? s(e) : p(void 0, t, n).then((() => o())).catch((e => s(e)));
  })))) : E(e, t, r, n), E = (h = (0, (a || n()).default)((function*(e, t, r, n) {
   const i = yield b(t, "w", n.mode);
   try {
    const r = yield m(e);
    yield _(i, r, 0, r.length), yield p(i, t, n);
   } finally {
    yield g(i);
   }
  })), function e(t, r, n, i) {
   return h.apply(this, arguments);
  }), x = t.fileDatesEqual = (e, t) => {
   const r = e.getTime(), n = t.getTime();
   if ("win32" !== process.platform) return r === n;
   if (Math.abs(r - n) <= 1) return !0;
   const i = Math.floor(r / 1e3), o = Math.floor(n / 1e3);
   return r - 1e3 * i == 0 || n - 1e3 * o == 0 ? i === o : r === n;
  };
 }, , , , , function(e, t, r) {
  function n() {
   return Boolean(process.env.FAKEROOTKEY);
  }
  function i(e) {
   return 0 === e;
  }
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.isFakeRoot = n, t.isRootUser = i, t.default = i(function o() {
   return "win32" !== process.platform && process.getuid ? process.getuid() : null;
  }()) && !n();
 }, , function(e, t, r) {
  function n() {
   return process.env.LOCALAPPDATA ? a.join(process.env.LOCALAPPDATA, "Yarn") : null;
  }
  Object.defineProperty(t, "__esModule", {
   value: !0
  }), t.getDataDir = function i() {
   if ("win32" === process.platform) {
    const e = n();
    return null == e ? l : a.join(e, "Data");
   }
   return process.env.XDG_DATA_HOME ? a.join(process.env.XDG_DATA_HOME, "yarn") : l;
  }, t.getCacheDir = function o() {
   return "win32" === process.platform ? a.join(n() || a.join(c, "AppData", "Local", "Yarn"), "Cache") : process.env.XDG_CACHE_HOME ? a.join(process.env.XDG_CACHE_HOME, "yarn") : "darwin" === process.platform ? a.join(c, "Library", "Caches", "Yarn") : u;
  }, t.getConfigDir = function s() {
   if ("win32" === process.platform) {
    const e = n();
    return null == e ? l : a.join(e, "Config");
   }
   return process.env.XDG_CONFIG_HOME ? a.join(process.env.XDG_CONFIG_HOME, "yarn") : l;
  };
  const a = r(0), c = r(45).default, l = a.join(c, ".config", "yarn"), u = a.join(c, ".cache", "yarn");
 }, , function(e, t, r) {
  e.exports = {
   default: r(179),
   __esModule: !0
  };
 }, function(e, t, r) {
  function n(e, t, r) {
   e instanceof RegExp && (e = i(e, r)), t instanceof RegExp && (t = i(t, r));
   var n = o(e, t, r);
   return n && {
    start: n[0],
    end: n[1],
    pre: r.slice(0, n[0]),
    body: r.slice(n[0] + e.length, n[1]),
    post: r.slice(n[1] + t.length)
   };
  }
  function i(e, t) {
   var r = t.match(e);
   return r ? r[0] : null;
  }
  function o(e, t, r) {
   var n, i, o, s, a, c = r.indexOf(e), l = r.indexOf(t, c + 1), u = c;
   if (c >= 0 && l > 0) {
    for (n = [], o = r.length; u >= 0 && !a; ) u == c ? (n.push(u), c = r.indexOf(e, u + 1)) : 1 == n.length ? a = [ n.pop(), l ] : ((i = n.pop()) < o && (o = i, 
    s = l), l = r.indexOf(t, u + 1)), u = c < l && c >= 0 ? c : l;
    n.length && (a = [ o, s ]);
   }
   return a;
  }
  e.exports = n, n.range = o;
 }, function(e, t, r) {
  function n(e) {
   return parseInt(e, 10) == e ? parseInt(e, 10) : e.charCodeAt(0);
  }
  function i(e) {
   return e.split(f).join("\\").split(h).join("{").split(d).join("}").split(p).join(",").split(m).join(".");
  }
  function o(e) {
   var t, r, n, i, s, a, c;
   return e ? (t = [], (r = y("{", "}", e)) ? (n = r.pre, i = r.body, s = r.post, (a = n.split(","))[a.length - 1] += "{" + i + "}", 
   c = o(s), s.length && (a[a.length - 1] += c.shift(), a.push.apply(a, c)), t.push.apply(t, a), 
   t) : e.split(",")) : [ "" ];
  }
  function s(e) {
   return "{" + e + "}";
  }
  function a(e) {
   return /^-?0\d/.test(e);
  }
  function c(e, t) {
   return e <= t;
  }
  function l(e, t) {
   return e >= t;
  }
  function u(e, t) {
   var r, i, f, h, p, m, b, v, _, w, k, E, x, O, S, C, A, j, T, P, F, L = [], $ = y("{", "}", e);
   if (!$ || /\$$/.test($.pre)) return [ e ];
   if (r = /^-?\d+\.\.-?\d+(?:\.\.-?\d+)?$/.test($.body), i = /^[a-zA-Z]\.\.[a-zA-Z](?:\.\.-?\d+)?$/.test($.body), 
   f = r || i, h = $.body.indexOf(",") >= 0, !f && !h) return $.post.match(/,.*\}/) ? u(e = $.pre + "{" + $.body + d + $.post) : [ e ];
   if (f) p = $.body.split(/\.\./); else if (1 === (p = o($.body)).length && 1 === (p = u(p[0], !1).map(s)).length) return (m = $.post.length ? u($.post, !1) : [ "" ]).map((function(e) {
    return $.pre + p[0] + e;
   }));
   if (b = $.pre, m = $.post.length ? u($.post, !1) : [ "" ], f) for (_ = n(p[0]), 
   w = n(p[1]), k = Math.max(p[0].length, p[1].length), E = 3 == p.length ? Math.abs(n(p[2])) : 1, 
   x = c, w < _ && (E *= -1, x = l), O = p.some(a), v = [], S = _; x(S, w); S += E) i ? "\\" === (C = String.fromCharCode(S)) && (C = "") : (C = String(S), 
   O && (A = k - C.length) > 0 && (j = new Array(A + 1).join("0"), C = S < 0 ? "-" + j + C.slice(1) : j + C)), 
   v.push(C); else v = g(p, (function(e) {
    return u(e, !1);
   }));
   for (T = 0; T < v.length; T++) for (P = 0; P < m.length; P++) F = b + v[T] + m[P], 
   (!t || f || F) && L.push(F);
   return L;
  }
  var f, h, d, p, m, g = r(178), y = r(174);
  e.exports = function b(e) {
   return e ? ("{}" === e.substr(0, 2) && (e = "\\{\\}" + e.substr(2)), u(function t(e) {
    return e.split("\\\\").join(f).split("\\{").join(h).split("\\}").join(d).split("\\,").join(p).split("\\.").join(m);
   }(e), !0).map(i)) : [];
  }, f = "\0SLASH" + Math.random() + "\0", h = "\0OPEN" + Math.random() + "\0", d = "\0CLOSE" + Math.random() + "\0", 
  p = "\0COMMA" + Math.random() + "\0", m = "\0PERIOD" + Math.random() + "\0";
 }, function(e, t, r) {
  function n(e) {
   let t = !1, r = !1, n = !1;
   for (let i = 0; i < e.length; i++) {
    const o = e[i];
    t && /[a-zA-Z]/.test(o) && o.toUpperCase() === o ? (e = e.substr(0, i) + "-" + e.substr(i), 
    t = !1, n = r, r = !0, i++) : r && n && /[a-zA-Z]/.test(o) && o.toLowerCase() === o ? (e = e.substr(0, i - 1) + "-" + e.substr(i - 1), 
    n = r, r = !1, t = !0) : (t = o.toLowerCase() === o, n = r, r = o.toUpperCase() === o);
   }
   return e;
  }
  e.exports = function(e) {
   if (0 === (e = arguments.length > 1 ? Array.from(arguments).map((e => e.trim())).filter((e => e.length)).join("-") : e.trim()).length) return "";
   if (1 === e.length) return e.toLowerCase();
   if (/^[a-z0-9]+$/.test(e)) return e;
   const t = e !== e.toLowerCase();
   return t && (e = n(e)), e.replace(/^[_.\- ]+/, "").toLowerCase().replace(/[_.\- ]+(\w|$)/g, ((e, t) => t.toUpperCase()));
  };
 }, , function(e, t) {
  e.exports = function(e, t) {
   var n, i, o = [];
   for (n = 0; n < e.length; n++) i = t(e[n], n), r(i) ? o.push.apply(o, i) : o.push(i);
   return o;
  };
  var r = Array.isArray || function(e) {
   return "[object Array]" === Object.prototype.toString.call(e);
  };
 }, function(e, t, r) {
  r(205), r(207), r(210), r(206), r(208), r(209), e.exports = r(23).Promise;
 }, function(e, t) {
  e.exports = function() {};
 }, function(e, t) {
  e.exports = function(e, t, r, n) {
   if (!(e instanceof t) || void 0 !== n && n in e) throw TypeError(r + ": incorrect invocation!");
   return e;
  };
 }, function(e, t, r) {
  var n = r(74), i = r(110), o = r(200);
  e.exports = function(e) {
   return function(t, r, s) {
    var a, c = n(t), l = i(c.length), u = o(s, l);
    if (e && r != r) {
     for (;l > u; ) if ((a = c[u++]) != a) return !0;
    } else for (;l > u; u++) if ((e || u in c) && c[u] === r) return e || u || 0;
    return !e && -1;
   };
  };
 }, function(e, t, r) {
  var n = r(48), i = r(187), o = r(186), s = r(27), a = r(110), c = r(203), l = {}, u = {};
  (t = e.exports = function(e, t, r, f, h) {
   var d, p, m, g, y = h ? function() {
    return e;
   } : c(e), b = n(r, f, t ? 2 : 1), v = 0;
   if ("function" != typeof y) throw TypeError(e + " is not iterable!");
   if (o(y)) {
    for (d = a(e.length); d > v; v++) if ((g = t ? b(s(p = e[v])[0], p[1]) : b(e[v])) === l || g === u) return g;
   } else for (m = y.call(e); !(p = m.next()).done; ) if ((g = i(m, b, p.value, t)) === l || g === u) return g;
  }).BREAK = l, t.RETURN = u;
 }, function(e, t, r) {
  e.exports = !r(33) && !r(85)((function() {
   return 7 != Object.defineProperty(r(68)("div"), "a", {
    get: function() {
     return 7;
    }
   }).a;
  }));
 }, function(e, t) {
  e.exports = function(e, t, r) {
   var n = void 0 === r;
   switch (t.length) {
   case 0:
    return n ? e() : e.call(r);

   case 1:
    return n ? e(t[0]) : e.call(r, t[0]);

   case 2:
    return n ? e(t[0], t[1]) : e.call(r, t[0], t[1]);

   case 3:
    return n ? e(t[0], t[1], t[2]) : e.call(r, t[0], t[1], t[2]);

   case 4:
    return n ? e(t[0], t[1], t[2], t[3]) : e.call(r, t[0], t[1], t[2], t[3]);
   }
   return e.apply(r, t);
  };
 }, function(e, t, r) {
  var n = r(35), i = r(13)("iterator"), o = Array.prototype;
  e.exports = function(e) {
   return void 0 !== e && (n.Array === e || o[i] === e);
  };
 }, function(e, t, r) {
  var n = r(27);
  e.exports = function(e, t, r, i) {
   try {
    return i ? t(n(r)[0], r[1]) : t(r);
   } catch (t) {
    var o = e.return;
    throw void 0 !== o && n(o.call(e)), t;
   }
  };
 }, function(e, t, r) {
  var n = r(192), i = r(106), o = r(71), s = {};
  r(31)(s, r(13)("iterator"), (function() {
   return this;
  })), e.exports = function(e, t, r) {
   e.prototype = n(s, {
    next: i(1, r)
   }), o(e, t + " Iterator");
  };
 }, function(e, t, r) {
  var n, i = r(13)("iterator"), o = !1;
  try {
   (n = [ 7 ][i]()).return = function() {
    o = !0;
   }, Array.from(n, (function() {
    throw 2;
   }));
  } catch (e) {}
  e.exports = function(e, t) {
   var r, n, s;
   if (!t && !o) return !1;
   r = !1;
   try {
    (s = (n = [ 7 ])[i]()).next = function() {
     return {
      done: r = !0
     };
    }, n[i] = function() {
     return s;
    }, e(n);
   } catch (e) {}
   return r;
  };
 }, function(e, t) {
  e.exports = function(e, t) {
   return {
    value: t,
    done: !!e
   };
  };
 }, function(e, t, r) {
  var n = r(11), i = r(109).set, o = n.MutationObserver || n.WebKitMutationObserver, s = n.process, a = n.Promise, c = "process" == r(47)(s);
  e.exports = function() {
   var e, t, r, l, u, f, h = function() {
    var n, i;
    for (c && (n = s.domain) && n.exit(); e; ) {
     i = e.fn, e = e.next;
     try {
      i();
     } catch (n) {
      throw e ? r() : t = void 0, n;
     }
    }
    t = void 0, n && n.enter();
   };
   return c ? r = function() {
    s.nextTick(h);
   } : !o || n.navigator && n.navigator.standalone ? a && a.resolve ? (f = a.resolve(void 0), 
   r = function() {
    f.then(h);
   }) : r = function() {
    i.call(n, h);
   } : (l = !0, u = document.createTextNode(""), new o(h).observe(u, {
    characterData: !0
   }), r = function() {
    u.data = l = !l;
   }), function(n) {
    var i = {
     fn: n,
     next: void 0
    };
    t && (t.next = i), e || (e = i, r()), t = i;
   };
  };
 }, function(e, t, r) {
  var n = r(27), i = r(193), o = r(101), s = r(72)("IE_PROTO"), a = function() {}, c = function() {
   var e, t = r(68)("iframe"), n = o.length;
   for (t.style.display = "none", r(102).appendChild(t), t.src = "javascript:", (e = t.contentWindow.document).open(), 
   e.write("<script>document.F=Object<\/script>"), e.close(), c = e.F; n--; ) delete c.prototype[o[n]];
   return c();
  };
  e.exports = Object.create || function e(t, r) {
   var o;
   return null !== t ? (a.prototype = n(t), o = new a, a.prototype = null, o[s] = t) : o = c(), 
   void 0 === r ? o : i(o, r);
  };
 }, function(e, t, r) {
  var n = r(50), i = r(27), o = r(132);
  e.exports = r(33) ? Object.defineProperties : function e(t, r) {
   var s, a, c, l;
   for (i(t), a = (s = o(r)).length, c = 0; a > c; ) n.f(t, l = s[c++], r[l]);
   return t;
  };
 }, function(e, t, r) {
  var n = r(49), i = r(133), o = r(72)("IE_PROTO"), s = Object.prototype;
  e.exports = Object.getPrototypeOf || function(e) {
   return e = i(e), n(e, o) ? e[o] : "function" == typeof e.constructor && e instanceof e.constructor ? e.constructor.prototype : e instanceof Object ? s : null;
  };
 }, function(e, t, r) {
  var n = r(49), i = r(74), o = r(182)(!1), s = r(72)("IE_PROTO");
  e.exports = function(e, t) {
   var r, a = i(e), c = 0, l = [];
   for (r in a) r != s && n(a, r) && l.push(r);
   for (;t.length > c; ) n(a, r = t[c++]) && (~o(l, r) || l.push(r));
   return l;
  };
 }, function(e, t, r) {
  var n = r(31);
  e.exports = function(e, t, r) {
   for (var i in t) r && e[i] ? e[i] = t[i] : n(e, i, t[i]);
   return e;
  };
 }, function(e, t, r) {
  e.exports = r(31);
 }, function(e, t, r) {
  var n = r(11), i = r(23), o = r(50), s = r(33), a = r(13)("species");
  e.exports = function(e) {
   var t = "function" == typeof i[e] ? i[e] : n[e];
   s && t && !t[a] && o.f(t, a, {
    configurable: !0,
    get: function() {
     return this;
    }
   });
  };
 }, function(e, t, r) {
  var n = r(73), i = r(67);
  e.exports = function(e) {
   return function(t, r) {
    var o, s, a = String(i(t)), c = n(r), l = a.length;
    return c < 0 || c >= l ? e ? "" : void 0 : (o = a.charCodeAt(c)) < 55296 || o > 56319 || c + 1 === l || (s = a.charCodeAt(c + 1)) < 56320 || s > 57343 ? e ? a.charAt(c) : o : e ? a.slice(c, c + 2) : s - 56320 + (o - 55296 << 10) + 65536;
   };
  };
 }, function(e, t, r) {
  var n = r(73), i = Math.max, o = Math.min;
  e.exports = function(e, t) {
   return (e = n(e)) < 0 ? i(e + t, 0) : o(e, t);
  };
 }, function(e, t, r) {
  var n = r(34);
  e.exports = function(e, t) {
   if (!n(e)) return e;
   var r, i;
   if (t && "function" == typeof (r = e.toString) && !n(i = r.call(e))) return i;
   if ("function" == typeof (r = e.valueOf) && !n(i = r.call(e))) return i;
   if (!t && "function" == typeof (r = e.toString) && !n(i = r.call(e))) return i;
   throw TypeError("Can't convert object to primitive value");
  };
 }, function(e, t, r) {
  var n = r(11).navigator;
  e.exports = n && n.userAgent || "";
 }, function(e, t, r) {
  var n = r(100), i = r(13)("iterator"), o = r(35);
  e.exports = r(23).getIteratorMethod = function(e) {
   if (null != e) return e[i] || e["@@iterator"] || o[n(e)];
  };
 }, function(e, t, r) {
  var n = r(180), i = r(190), o = r(35), s = r(74);
  e.exports = r(103)(Array, "Array", (function(e, t) {
   this._t = s(e), this._i = 0, this._k = t;
  }), (function() {
   var e = this._t, t = this._k, r = this._i++;
   return !e || r >= e.length ? (this._t = void 0, i(1)) : i(0, "keys" == t ? r : "values" == t ? e[r] : [ r, e[r] ]);
  }), "values"), o.Arguments = o.Array, n("keys"), n("values"), n("entries");
 }, function(e, t) {}, function(e, t, r) {
  var n, i, o, s, a = r(69), c = r(11), l = r(48), u = r(100), f = r(41), h = r(34), d = r(46), p = r(181), m = r(183), g = r(108), y = r(109).set, b = r(191)(), v = r(70), _ = r(104), w = r(202), k = r(105), E = "Promise", x = c.TypeError, O = c.process, S = O && O.versions, C = S && S.v8 || "", A = c.Promise, j = "process" == u(O), T = function() {}, P = i = v.f, F = !!function() {
   var e, t;
   try {
    return t = ((e = A.resolve(1)).constructor = {})[r(13)("species")] = function(e) {
     e(T, T);
    }, (j || "function" == typeof PromiseRejectionEvent) && e.then(T) instanceof t && 0 !== C.indexOf("6.6") && -1 === w.indexOf("Chrome/66");
   } catch (e) {}
  }(), L = function(e) {
   var t;
   return !(!h(e) || "function" != typeof (t = e.then)) && t;
  }, $ = function(e, t) {
   if (!e._n) {
    e._n = !0;
    var r = e._c;
    b((function() {
     for (var n = e._v, i = 1 == e._s, o = 0, s = function(t) {
      var r, o, s, a = i ? t.ok : t.fail, c = t.resolve, l = t.reject, u = t.domain;
      try {
       a ? (i || (2 == e._h && R(e), e._h = 1), !0 === a ? r = n : (u && u.enter(), r = a(n), 
       u && (u.exit(), s = !0)), r === t.promise ? l(x("Promise-chain cycle")) : (o = L(r)) ? o.call(r, c, l) : c(r)) : l(n);
      } catch (e) {
       u && !s && u.exit(), l(e);
      }
     }; r.length > o; ) s(r[o++]);
     e._c = [], e._n = !1, t && !e._h && N(e);
    }));
   }
  }, N = function(e) {
   y.call(c, (function() {
    var t, r, n, i = e._v, o = D(e);
    if (o && (t = _((function() {
     j ? O.emit("unhandledRejection", i, e) : (r = c.onunhandledrejection) ? r({
      promise: e,
      reason: i
     }) : (n = c.console) && n.error && n.error("Unhandled promise rejection", i);
    })), e._h = j || D(e) ? 2 : 1), e._a = void 0, o && t.e) throw t.v;
   }));
  }, D = function(e) {
   return 1 !== e._h && 0 === (e._a || e._c).length;
  }, R = function(e) {
   y.call(c, (function() {
    var t;
    j ? O.emit("rejectionHandled", e) : (t = c.onrejectionhandled) && t({
     promise: e,
     reason: e._v
    });
   }));
  }, M = function(e) {
   var t = this;
   t._d || (t._d = !0, (t = t._w || t)._v = e, t._s = 2, t._a || (t._a = t._c.slice()), 
   $(t, !0));
  }, I = function(e) {
   var t, r = this;
   if (!r._d) {
    r._d = !0, r = r._w || r;
    try {
     if (r === e) throw x("Promise can't be resolved itself");
     (t = L(e)) ? b((function() {
      var n = {
       _w: r,
       _d: !1
      };
      try {
       t.call(e, l(I, n, 1), l(M, n, 1));
      } catch (e) {
       M.call(n, e);
      }
     })) : (r._v = e, r._s = 1, $(r, !1));
    } catch (e) {
     M.call({
      _w: r,
      _d: !1
     }, e);
    }
   }
  };
  F || (A = function e(t) {
   p(this, A, E, "_h"), d(t), n.call(this);
   try {
    t(l(I, this, 1), l(M, this, 1));
   } catch (e) {
    M.call(this, e);
   }
  }, (n = function e(t) {
   this._c = [], this._a = void 0, this._s = 0, this._d = !1, this._v = void 0, this._h = 0, 
   this._n = !1;
  }).prototype = r(196)(A.prototype, {
   then: function e(t, r) {
    var n = P(g(this, A));
    return n.ok = "function" != typeof t || t, n.fail = "function" == typeof r && r, 
    n.domain = j ? O.domain : void 0, this._c.push(n), this._a && this._a.push(n), this._s && $(this, !1), 
    n.promise;
   },
   catch: function(e) {
    return this.then(void 0, e);
   }
  }), o = function() {
   var e = new n;
   this.promise = e, this.resolve = l(I, e, 1), this.reject = l(M, e, 1);
  }, v.f = P = function(e) {
   return e === A || e === s ? new o(e) : i(e);
  }), f(f.G + f.W + f.F * !F, {
   Promise: A
  }), r(71)(A, E), r(198)(E), s = r(23).Promise, f(f.S + f.F * !F, E, {
   reject: function e(t) {
    var r = P(this);
    return (0, r.reject)(t), r.promise;
   }
  }), f(f.S + f.F * (a || !F), E, {
   resolve: function e(t) {
    return k(a && this === s ? A : this, t);
   }
  }), f(f.S + f.F * !(F && r(189)((function(e) {
   A.all(e).catch(T);
  }))), E, {
   all: function e(t) {
    var r = this, n = P(r), i = n.resolve, o = n.reject, s = _((function() {
     var e = [], n = 0, s = 1;
     m(t, !1, (function(t) {
      var a = n++, c = !1;
      e.push(void 0), s++, r.resolve(t).then((function(t) {
       c || (c = !0, e[a] = t, --s || i(e));
      }), o);
     })), --s || i(e);
    }));
    return s.e && o(s.v), n.promise;
   },
   race: function e(t) {
    var r = this, n = P(r), i = n.reject, o = _((function() {
     m(t, !1, (function(e) {
      r.resolve(e).then(n.resolve, i);
     }));
    }));
    return o.e && i(o.v), n.promise;
   }
  });
 }, function(e, t, r) {
  var n = r(199)(!0);
  r(103)(String, "String", (function(e) {
   this._t = String(e), this._i = 0;
  }), (function() {
   var e, t = this._t, r = this._i;
   return r >= t.length ? {
    value: void 0,
    done: !0
   } : (e = n(t, r), this._i += e.length, {
    value: e,
    done: !1
   });
  }));
 }, function(e, t, r) {
  var n = r(41), i = r(23), o = r(11), s = r(108), a = r(105);
  n(n.P + n.R, "Promise", {
   finally: function(e) {
    var t = s(this, i.Promise || o.Promise), r = "function" == typeof e;
    return this.then(r ? function(r) {
     return a(t, e()).then((function() {
      return r;
     }));
    } : e, r ? function(r) {
     return a(t, e()).then((function() {
      throw r;
     }));
    } : e);
   }
  });
 }, function(e, t, r) {
  var n = r(41), i = r(70), o = r(104);
  n(n.S, "Promise", {
   try: function(e) {
    var t = i.f(this), r = o(e);
    return (r.e ? t.reject : t.resolve)(r.v), t.promise;
   }
  });
 }, function(e, t, r) {
  var n, i, o, s, a, c, l, u, f;
  for (r(204), n = r(11), i = r(31), o = r(35), s = r(13)("toStringTag"), a = "CSSRuleList,CSSStyleDeclaration,CSSValueList,ClientRectList,DOMRectList,DOMStringList,DOMTokenList,DataTransferItemList,FileList,HTMLAllCollection,HTMLCollection,HTMLFormElement,HTMLSelectElement,MediaList,MimeTypeArray,NamedNodeMap,NodeList,PaintRequestList,Plugin,PluginArray,SVGLengthList,SVGNumberList,SVGPathSegList,SVGPointList,SVGStringList,SVGTransformList,SourceBufferList,StyleSheetList,TextTrackCueList,TextTrackList,TouchList".split(","), 
  c = 0; c < a.length; c++) (f = (u = n[l = a[c]]) && u.prototype) && !f[s] && i(f, s, l), 
  o[l] = o.Array;
 }, function(e, t, r) {
  function n() {
   var e;
   try {
    e = t.storage.debug;
   } catch (e) {}
   return !e && "undefined" != typeof process && "env" in process && (e = process.env.DEBUG), 
   e;
  }
  (t = e.exports = r(112)).log = function i() {
   return "object" == typeof console && console.log && Function.prototype.apply.call(console.log, console, arguments);
  }, t.formatArgs = function o(e) {
   var r, n, i, o = this.useColors;
   e[0] = (o ? "%c" : "") + this.namespace + (o ? " %c" : " ") + e[0] + (o ? "%c " : " ") + "+" + t.humanize(this.diff), 
   o && (r = "color: " + this.color, e.splice(1, 0, r, "color: inherit"), n = 0, i = 0, 
   e[0].replace(/%[a-zA-Z%]/g, (function(e) {
    "%%" !== e && (n++, "%c" === e && (i = n));
   })), e.splice(i, 0, r));
  }, t.save = function s(e) {
   try {
    null == e ? t.storage.removeItem("debug") : t.storage.debug = e;
   } catch (e) {}
  }, t.load = n, t.useColors = function a() {
   return !("undefined" == typeof window || !window.process || "renderer" !== window.process.type) || ("undefined" == typeof navigator || !navigator.userAgent || !navigator.userAgent.toLowerCase().match(/(edge|trident)\/(\d+)/)) && ("undefined" != typeof document && document.documentElement && document.documentElement.style && document.documentElement.style.WebkitAppearance || "undefined" != typeof window && window.console && (window.console.firebug || window.console.exception && window.console.table) || "undefined" != typeof navigator && navigator.userAgent && navigator.userAgent.toLowerCase().match(/firefox\/(\d+)/) && parseInt(RegExp.$1, 10) >= 31 || "undefined" != typeof navigator && navigator.userAgent && navigator.userAgent.toLowerCase().match(/applewebkit\/(\d+)/));
  }, t.storage = "undefined" != typeof chrome && void 0 !== chrome.storage ? chrome.storage.local : function c() {
   try {
    return window.localStorage;
   } catch (e) {}
  }(), t.colors = [ "#0000CC", "#0000FF", "#0033CC", "#0033FF", "#0066CC", "#0066FF", "#0099CC", "#0099FF", "#00CC00", "#00CC33", "#00CC66", "#00CC99", "#00CCCC", "#00CCFF", "#3300CC", "#3300FF", "#3333CC", "#3333FF", "#3366CC", "#3366FF", "#3399CC", "#3399FF", "#33CC00", "#33CC33", "#33CC66", "#33CC99", "#33CCCC", "#33CCFF", "#6600CC", "#6600FF", "#6633CC", "#6633FF", "#66CC00", "#66CC33", "#9900CC", "#9900FF", "#9933CC", "#9933FF", "#99CC00", "#99CC33", "#CC0000", "#CC0033", "#CC0066", "#CC0099", "#CC00CC", "#CC00FF", "#CC3300", "#CC3333", "#CC3366", "#CC3399", "#CC33CC", "#CC33FF", "#CC6600", "#CC6633", "#CC9900", "#CC9933", "#CCCC00", "#CCCC33", "#FF0000", "#FF0033", "#FF0066", "#FF0099", "#FF00CC", "#FF00FF", "#FF3300", "#FF3333", "#FF3366", "#FF3399", "#FF33CC", "#FF33FF", "#FF6600", "#FF6633", "#FF9900", "#FF9933", "#FFCC00", "#FFCC33" ], 
  t.formatters.j = function(e) {
   try {
    return JSON.stringify(e);
   } catch (e) {
    return "[UnexpectedJSONParseError]: " + e.message;
   }
  }, t.enable(n());
 }, function(e, t, r) {
  "undefined" == typeof process || "renderer" === process.type ? e.exports = r(211) : e.exports = r(213);
 }, function(e, t, r) {
  function n() {
   return process.env.DEBUG;
  }
  var i, o = r(79), s = r(2);
  (t = e.exports = r(112)).init = function a(e) {
   var r, n;
   for (e.inspectOpts = {}, r = Object.keys(t.inspectOpts), n = 0; n < r.length; n++) e.inspectOpts[r[n]] = t.inspectOpts[r[n]];
  }, t.log = function c() {
   return process.stderr.write(s.format.apply(s, arguments) + "\n");
  }, t.formatArgs = function l(e) {
   var r, n, i, o = this.namespace;
   this.useColors ? (i = "  " + (n = "[3" + ((r = this.color) < 8 ? r : "8;5;" + r)) + ";1m" + o + " [0m", 
   e[0] = i + e[0].split("\n").join("\n" + i), e.push(n + "m+" + t.humanize(this.diff) + "[0m")) : e[0] = function s() {
    return t.inspectOpts.hideDate ? "" : (new Date).toISOString() + " ";
   }() + o + " " + e[0];
  }, t.save = function u(e) {
   null == e ? delete process.env.DEBUG : process.env.DEBUG = e;
  }, t.load = n, t.useColors = function f() {
   return "colors" in t.inspectOpts ? Boolean(t.inspectOpts.colors) : o.isatty(process.stderr.fd);
  }, t.colors = [ 6, 2, 3, 4, 5, 1 ];
  try {
   (i = r(239)) && i.level >= 2 && (t.colors = [ 20, 21, 26, 27, 32, 33, 38, 39, 40, 41, 42, 43, 44, 45, 56, 57, 62, 63, 68, 69, 74, 75, 76, 77, 78, 79, 80, 81, 92, 93, 98, 99, 112, 113, 128, 129, 134, 135, 148, 149, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 178, 179, 184, 185, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 214, 215, 220, 221 ]);
  } catch (e) {}
  t.inspectOpts = Object.keys(process.env).filter((function(e) {
   return /^debug_/i.test(e);
  })).reduce((function(e, t) {
   var r = t.substring(6).toLowerCase().replace(/_([a-z])/g, (function(e, t) {
    return t.toUpperCase();
   })), n = process.env[t];
   return n = !!/^(yes|on|true|enabled)$/i.test(n) || !/^(no|off|false|disabled)$/i.test(n) && ("null" === n ? null : Number(n)), 
   e[r] = n, e;
  }), {}), t.formatters.o = function(e) {
   return this.inspectOpts.colors = this.useColors, s.inspect(e, this.inspectOpts).split("\n").map((function(e) {
    return e.trim();
   })).join(" ");
  }, t.formatters.O = function(e) {
   return this.inspectOpts.colors = this.useColors, s.inspect(e, this.inspectOpts);
  }, t.enable(n());
 }, , , , function(e, t, r) {
  var n, i, o = r(0), s = "win32" === process.platform, a = r(3), c = process.env.NODE_DEBUG && /fs/.test(process.env.NODE_DEBUG);
  n = s ? /(.*?)(?:[\/\\]+|$)/g : /(.*?)(?:[\/]+|$)/g, i = s ? /^(?:[a-zA-Z]:|[\\\/]{2}[^\\\/]+[\\\/][^\\\/]+)?[\\\/]*/ : /^[\/]*/, 
  t.realpathSync = function e(t, r) {
   function c() {
    var e = i.exec(t);
    h = e[0].length, d = e[0], p = e[0], m = "", s && !f[p] && (a.lstatSync(p), f[p] = !0);
   }
   var l, u, f, h, d, p, m, g, y, b, v, _;
   if (t = o.resolve(t), r && Object.prototype.hasOwnProperty.call(r, t)) return r[t];
   for (l = t, u = {}, f = {}, c(); h < t.length; ) if (n.lastIndex = h, g = n.exec(t), 
   m = d, d += g[0], p = m + g[1], h = n.lastIndex, !(f[p] || r && r[p] === p)) {
    if (r && Object.prototype.hasOwnProperty.call(r, p)) y = r[p]; else {
     if (!(b = a.lstatSync(p)).isSymbolicLink()) {
      f[p] = !0, r && (r[p] = p);
      continue;
     }
     v = null, s || (_ = b.dev.toString(32) + ":" + b.ino.toString(32), u.hasOwnProperty(_) && (v = u[_])), 
     null === v && (a.statSync(p), v = a.readlinkSync(p)), y = o.resolve(m, v), r && (r[p] = y), 
     s || (u[_] = v);
    }
    t = o.resolve(y, t.slice(h)), c();
   }
   return r && (r[l] = t), t;
  }, t.realpath = function e(t, r, l) {
   function u() {
    var e = i.exec(t);
    b = e[0].length, v = e[0], _ = e[0], w = "", s && !y[_] ? a.lstat(_, (function(e) {
     if (e) return l(e);
     y[_] = !0, f();
    })) : process.nextTick(f);
   }
   function f() {
    if (b >= t.length) return r && (r[m] = t), l(null, t);
    n.lastIndex = b;
    var e = n.exec(t);
    return w = v, v += e[0], _ = w + e[1], b = n.lastIndex, y[_] || r && r[_] === _ ? process.nextTick(f) : r && Object.prototype.hasOwnProperty.call(r, _) ? p(r[_]) : a.lstat(_, h);
   }
   function h(e, t) {
    if (e) return l(e);
    if (!t.isSymbolicLink()) return y[_] = !0, r && (r[_] = _), process.nextTick(f);
    if (!s) {
     var n = t.dev.toString(32) + ":" + t.ino.toString(32);
     if (g.hasOwnProperty(n)) return d(null, g[n], _);
    }
    a.stat(_, (function(e) {
     if (e) return l(e);
     a.readlink(_, (function(e, t) {
      s || (g[n] = t), d(e, t);
     }));
    }));
   }
   function d(e, t, n) {
    if (e) return l(e);
    var i = o.resolve(w, t);
    r && (r[n] = i), p(i);
   }
   function p(e) {
    t = o.resolve(e, t.slice(b)), u();
   }
   var m, g, y, b, v, _, w;
   if ("function" != typeof l && (l = function k(e) {
    return "function" == typeof e ? e : function t() {
     function e(e) {
      if (e) {
       if (process.throwDeprecation) throw e;
       if (!process.noDeprecation) {
        var t = "fs: missing callback " + (e.stack || e.message);
        process.traceDeprecation ? console.trace(t) : console.error(t);
       }
      }
     }
     var t, r;
     return c ? (r = new Error, t = function n(t) {
      t && (r.message = t.message, e(t = r));
     }) : t = e, t;
    }();
   }(r), r = null), t = o.resolve(t), r && Object.prototype.hasOwnProperty.call(r, t)) return process.nextTick(l.bind(null, null, r[t]));
   m = t, g = {}, y = {}, u();
  };
 }, function(e, t, r) {
  function n(e, t) {
   if ("function" == typeof t || 3 === arguments.length) throw new TypeError("callback provided to sync glob\nSee: https://github.com/isaacs/node-glob/issues/167");
   return new i(e, t).found;
  }
  function i(e, t) {
   var r, n;
   if (!e) throw new Error("must provide pattern");
   if ("function" == typeof t || 3 === arguments.length) throw new TypeError("callback provided to sync glob\nSee: https://github.com/isaacs/node-glob/issues/167");
   if (!(this instanceof i)) return new i(e, t);
   if (h(this, e, t), this.noprocess) return this;
   for (r = this.minimatch.set.length, this.matches = new Array(r), n = 0; n < r; n++) this._process(this.minimatch.set[n], n, !1);
   this._finish();
  }
  var o, s, a, c, l, u, f, h, d, p, m;
  e.exports = n, n.GlobSync = i, o = r(3), s = r(114), a = r(60), r(75).Glob, r(2), 
  c = r(0), l = r(22), u = r(76), f = r(115), h = f.setopts, d = f.ownProp, p = f.childrenIgnored, 
  m = f.isIgnored, i.prototype._finish = function() {
   if (l(this instanceof i), this.realpath) {
    var e = this;
    this.matches.forEach((function(t, r) {
     var n, i = e.matches[r] = Object.create(null);
     for (n in t) try {
      n = e._makeAbs(n), i[s.realpathSync(n, e.realpathCache)] = !0;
     } catch (t) {
      if ("stat" !== t.syscall) throw t;
      i[e._makeAbs(n)] = !0;
     }
    }));
   }
   f.finish(this);
  }, i.prototype._process = function(e, t, r) {
   var n, o, s, c, f;
   for (l(this instanceof i), n = 0; "string" == typeof e[n]; ) n++;
   switch (n) {
   case e.length:
    return void this._processSimple(e.join("/"), t);

   case 0:
    o = null;
    break;

   default:
    o = e.slice(0, n).join("/");
   }
   s = e.slice(n), null === o ? c = "." : u(o) || u(e.join("/")) ? (o && u(o) || (o = "/" + o), 
   c = o) : c = o, f = this._makeAbs(c), p(this, c) || (s[0] === a.GLOBSTAR ? this._processGlobStar(o, c, f, s, t, r) : this._processReaddir(o, c, f, s, t, r));
  }, i.prototype._processReaddir = function(e, t, r, n, i, o) {
   var s, a, l, u, f, h, d, p, m, g = this._readdir(r, o);
   if (g) {
    for (s = n[0], a = !!this.minimatch.negate, l = s._glob, u = this.dot || "." === l.charAt(0), 
    f = [], h = 0; h < g.length; h++) ("." !== (d = g[h]).charAt(0) || u) && (a && !e ? !d.match(s) : d.match(s)) && f.push(d);
    if (0 !== (p = f.length)) if (1 !== n.length || this.mark || this.stat) for (n.shift(), 
    h = 0; h < p; h++) d = f[h], m = e ? [ e, d ] : [ d ], this._process(m.concat(n), i, o); else for (this.matches[i] || (this.matches[i] = Object.create(null)), 
    h = 0; h < p; h++) d = f[h], e && (d = "/" !== e.slice(-1) ? e + "/" + d : e + d), 
    "/" !== d.charAt(0) || this.nomount || (d = c.join(this.root, d)), this._emitMatch(i, d);
   }
  }, i.prototype._emitMatch = function(e, t) {
   var r, n;
   m(this, t) || (r = this._makeAbs(t), this.mark && (t = this._mark(t)), this.absolute && (t = r), 
   this.matches[e][t] || this.nodir && ("DIR" === (n = this.cache[r]) || Array.isArray(n)) || (this.matches[e][t] = !0, 
   this.stat && this._stat(t)));
  }, i.prototype._readdirInGlobStar = function(e) {
   var t, r, n;
   if (this.follow) return this._readdir(e, !1);
   try {
    r = o.lstatSync(e);
   } catch (e) {
    if ("ENOENT" === e.code) return null;
   }
   return n = r && r.isSymbolicLink(), this.symlinks[e] = n, n || !r || r.isDirectory() ? t = this._readdir(e, !1) : this.cache[e] = "FILE", 
   t;
  }, i.prototype._readdir = function(e, t) {
   if (t && !d(this.symlinks, e)) return this._readdirInGlobStar(e);
   if (d(this.cache, e)) {
    var r = this.cache[e];
    if (!r || "FILE" === r) return null;
    if (Array.isArray(r)) return r;
   }
   try {
    return this._readdirEntries(e, o.readdirSync(e));
   } catch (t) {
    return this._readdirError(e, t), null;
   }
  }, i.prototype._readdirEntries = function(e, t) {
   var r, n;
   if (!this.mark && !this.stat) for (r = 0; r < t.length; r++) n = t[r], n = "/" === e ? e + n : e + "/" + n, 
   this.cache[n] = !0;
   return this.cache[e] = t, t;
  }, i.prototype._readdirError = function(e, t) {
   var r, n;
   switch (t.code) {
   case "ENOTSUP":
   case "ENOTDIR":
    if (r = this._makeAbs(e), this.cache[r] = "FILE", r === this.cwdAbs) throw (n = new Error(t.code + " invalid cwd " + this.cwd)).path = this.cwd, 
    n.code = t.code, n;
    break;

   case "ENOENT":
   case "ELOOP":
   case "ENAMETOOLONG":
   case "UNKNOWN":
    this.cache[this._makeAbs(e)] = !1;
    break;

   default:
    if (this.cache[this._makeAbs(e)] = !1, this.strict) throw t;
    this.silent || console.error("glob error", t);
   }
  }, i.prototype._processGlobStar = function(e, t, r, n, i, o) {
   var s, a, c, l, u, f, h, d = this._readdir(r, o);
   if (d && (s = n.slice(1), c = (a = e ? [ e ] : []).concat(s), this._process(c, i, !1), 
   l = d.length, !this.symlinks[r] || !o)) for (u = 0; u < l; u++) ("." !== d[u].charAt(0) || this.dot) && (f = a.concat(d[u], s), 
   this._process(f, i, !0), h = a.concat(d[u], n), this._process(h, i, !0));
  }, i.prototype._processSimple = function(e, t) {
   var r, n = this._stat(e);
   this.matches[t] || (this.matches[t] = Object.create(null)), n && (e && u(e) && !this.nomount && (r = /[\/\\]$/.test(e), 
   "/" === e.charAt(0) ? e = c.join(this.root, e) : (e = c.resolve(this.root, e), r && (e += "/"))), 
   "win32" === process.platform && (e = e.replace(/\\/g, "/")), this._emitMatch(t, e));
  }, i.prototype._stat = function(e) {
   var t, r, n, i = this._makeAbs(e), s = "/" === e.slice(-1);
   if (e.length > this.maxLength) return !1;
   if (!this.stat && d(this.cache, i)) {
    if (t = this.cache[i], Array.isArray(t) && (t = "DIR"), !s || "DIR" === t) return t;
    if (s && "FILE" === t) return !1;
   }
   if (!(r = this.statCache[i])) {
    try {
     n = o.lstatSync(i);
    } catch (e) {
     if (e && ("ENOENT" === e.code || "ENOTDIR" === e.code)) return this.statCache[i] = !1, 
     !1;
    }
    if (n && n.isSymbolicLink()) try {
     r = o.statSync(i);
    } catch (e) {
     r = n;
    } else r = n;
   }
   return this.statCache[i] = r, t = !0, r && (t = r.isDirectory() ? "DIR" : "FILE"), 
   this.cache[i] = this.cache[i] || t, (!s || "FILE" !== t) && t;
  }, i.prototype._mark = function(e) {
   return f.mark(this, e);
  }, i.prototype._makeAbs = function(e) {
   return f.makeAbs(this, e);
  };
 }, , , function(e, t, r) {
  e.exports = function(e, t) {
   var r, n, i;
   return r = (t = t || process.argv).indexOf("--"), n = /^--/.test(e) ? "" : "--", 
   -1 !== (i = t.indexOf(n + e)) && (-1 === r || i < r);
  };
 }, , function(e, t, r) {
  function n(e) {
   var t, r = e.length, n = [];
   for (t = 0; t < r; t++) n[t] = e[t];
   return n;
  }
  var i = r(123), o = Object.create(null), s = r(61);
  e.exports = i((function a(e, t) {
   return o[e] ? (o[e].push(t), null) : (o[e] = [ t ], function r(e) {
    return s((function t() {
     var r, i = o[e], s = i.length, a = n(arguments);
     try {
      for (r = 0; r < s; r++) i[r].apply(null, a);
     } finally {
      i.length > s ? (i.splice(0, s), process.nextTick((function() {
       t.apply(null, a);
      }))) : delete o[e];
     }
    }));
   }(e));
  }));
 }, function(e, t) {
  "function" == typeof Object.create ? e.exports = function e(t, r) {
   t.super_ = r, t.prototype = Object.create(r.prototype, {
    constructor: {
     value: t,
     enumerable: !1,
     writable: !0,
     configurable: !0
    }
   });
  } : e.exports = function e(t, r) {
   t.super_ = r;
   var n = function() {};
   n.prototype = r.prototype, t.prototype = new n, t.prototype.constructor = t;
  };
 }, , , function(e, t, r) {
  e.exports = void 0 !== r;
 }, , function(e, t) {
  function r(e, t, r) {
   if (!(e < t)) return e < 1.5 * t ? Math.floor(e / t) + " " + r : Math.ceil(e / t) + " " + r + "s";
  }
  var n = 1e3, i = 60 * n, o = 60 * i, s = 24 * o;
  e.exports = function(e, t) {
   t = t || {};
   var a = typeof e;
   if ("string" === a && e.length > 0) return function c(e) {
    var t, r;
    if (!((e = String(e)).length > 100) && (t = /^((?:\d+)?\.?\d+) *(milliseconds?|msecs?|ms|seconds?|secs?|s|minutes?|mins?|m|hours?|hrs?|h|days?|d|years?|yrs?|y)?$/i.exec(e))) switch (r = parseFloat(t[1]), 
    (t[2] || "ms").toLowerCase()) {
    case "years":
    case "year":
    case "yrs":
    case "yr":
    case "y":
     return 315576e5 * r;

    case "days":
    case "day":
    case "d":
     return r * s;

    case "hours":
    case "hour":
    case "hrs":
    case "hr":
    case "h":
     return r * o;

    case "minutes":
    case "minute":
    case "mins":
    case "min":
    case "m":
     return r * i;

    case "seconds":
    case "second":
    case "secs":
    case "sec":
    case "s":
     return r * n;

    case "milliseconds":
    case "millisecond":
    case "msecs":
    case "msec":
    case "ms":
     return r;

    default:
     return;
    }
   }(e);
   if ("number" === a && !1 === isNaN(e)) return t.long ? function l(e) {
    return r(e, s, "day") || r(e, o, "hour") || r(e, i, "minute") || r(e, n, "second") || e + " ms";
   }(e) : function u(e) {
    return e >= s ? Math.round(e / s) + "d" : e >= o ? Math.round(e / o) + "h" : e >= i ? Math.round(e / i) + "m" : e >= n ? Math.round(e / n) + "s" : e + "ms";
   }(e);
   throw new Error("val is not a non-empty string or a valid number. val=" + JSON.stringify(e));
  };
 }, , , , function(e, t, r) {
  function n(e) {
   [ "unlink", "chmod", "stat", "lstat", "rmdir", "readdir" ].forEach((function(t) {
    e[t] = e[t] || d[t], e[t += "Sync"] = e[t] || d[t];
   })), e.maxBusyTries = e.maxBusyTries || 3, e.emfileWait = e.emfileWait || 1e3, !1 === e.glob && (e.disableGlob = !0), 
   e.disableGlob = e.disableGlob || !1, e.glob = e.glob || g;
  }
  function i(e, t, r) {
   function i(e, n) {
    return e ? r(e) : 0 === (c = n.length) ? r() : void n.forEach((function(e) {
     o(e, t, (function n(i) {
      if (i) {
       if (("EBUSY" === i.code || "ENOTEMPTY" === i.code || "EPERM" === i.code) && s < t.maxBusyTries) return s++, 
       setTimeout((function() {
        o(e, t, n);
       }), 100 * s);
       if ("EMFILE" === i.code && y < t.emfileWait) return setTimeout((function() {
        o(e, t, n);
       }), y++);
       "ENOENT" === i.code && (i = null);
      }
      y = 0, function l(e) {
       a = a || e, 0 == --c && r(a);
      }(i);
     }));
    }));
   }
   var s, a, c;
   if ("function" == typeof t && (r = t, t = {}), f(e, "rimraf: missing path"), f.equal(typeof e, "string", "rimraf: path should be a string"), 
   f.equal(typeof r, "function", "rimraf: callback function required"), f(t, "rimraf: invalid options argument provided"), 
   f.equal(typeof t, "object", "rimraf: options should be object"), n(t), s = 0, a = null, 
   c = 0, t.disableGlob || !p.hasMagic(e)) return i(null, [ e ]);
   t.lstat(e, (function(r, n) {
    if (!r) return i(null, [ e ]);
    p(e, t.glob, i);
   }));
  }
  function o(e, t, r) {
   f(e), f(t), f("function" == typeof r), t.lstat(e, (function(n, i) {
    return n && "ENOENT" === n.code ? r(null) : (n && "EPERM" === n.code && b && s(e, t, n, r), 
    i && i.isDirectory() ? c(e, t, n, r) : void t.unlink(e, (function(n) {
     if (n) {
      if ("ENOENT" === n.code) return r(null);
      if ("EPERM" === n.code) return b ? s(e, t, n, r) : c(e, t, n, r);
      if ("EISDIR" === n.code) return c(e, t, n, r);
     }
     return r(n);
    })));
   }));
  }
  function s(e, t, r, n) {
   f(e), f(t), f("function" == typeof n), r && f(r instanceof Error), t.chmod(e, m, (function(i) {
    i ? n("ENOENT" === i.code ? null : r) : t.stat(e, (function(i, o) {
     i ? n("ENOENT" === i.code ? null : r) : o.isDirectory() ? c(e, t, r, n) : t.unlink(e, n);
    }));
   }));
  }
  function a(e, t, r) {
   f(e), f(t), r && f(r instanceof Error);
   try {
    t.chmodSync(e, m);
   } catch (e) {
    if ("ENOENT" === e.code) return;
    throw r;
   }
   try {
    var n = t.statSync(e);
   } catch (e) {
    if ("ENOENT" === e.code) return;
    throw r;
   }
   n.isDirectory() ? u(e, t, r) : t.unlinkSync(e);
  }
  function c(e, t, r, n) {
   f(e), f(t), r && f(r instanceof Error), f("function" == typeof n), t.rmdir(e, (function(o) {
    !o || "ENOTEMPTY" !== o.code && "EEXIST" !== o.code && "EPERM" !== o.code ? o && "ENOTDIR" === o.code ? n(r) : n(o) : function s(e, t, r) {
     f(e), f(t), f("function" == typeof r), t.readdir(e, (function(n, o) {
      var s, a;
      return n ? r(n) : 0 === (s = o.length) ? t.rmdir(e, r) : void o.forEach((function(n) {
       i(h.join(e, n), t, (function(n) {
        if (!a) return n ? r(a = n) : void (0 == --s && t.rmdir(e, r));
       }));
      }));
     }));
    }(e, t, n);
   }));
  }
  function l(e, t) {
   var r, i, o;
   if (n(t = t || {}), f(e, "rimraf: missing path"), f.equal(typeof e, "string", "rimraf: path should be a string"), 
   f(t, "rimraf: missing options"), f.equal(typeof t, "object", "rimraf: options should be object"), 
   t.disableGlob || !p.hasMagic(e)) r = [ e ]; else try {
    t.lstatSync(e), r = [ e ];
   } catch (n) {
    r = p.sync(e, t.glob);
   }
   if (r.length) for (i = 0; i < r.length; i++) {
    e = r[i];
    try {
     o = t.lstatSync(e);
    } catch (r) {
     if ("ENOENT" === r.code) return;
     "EPERM" === r.code && b && a(e, t, r);
    }
    try {
     o && o.isDirectory() ? u(e, t, null) : t.unlinkSync(e);
    } catch (r) {
     if ("ENOENT" === r.code) return;
     if ("EPERM" === r.code) return b ? a(e, t, r) : u(e, t, r);
     if ("EISDIR" !== r.code) throw r;
     u(e, t, r);
    }
   }
  }
  function u(e, t, r) {
   f(e), f(t), r && f(r instanceof Error);
   try {
    t.rmdirSync(e);
   } catch (n) {
    if ("ENOENT" === n.code) return;
    if ("ENOTDIR" === n.code) throw r;
    "ENOTEMPTY" !== n.code && "EEXIST" !== n.code && "EPERM" !== n.code || function n(e, t) {
     var r, n, i, o;
     for (f(e), f(t), t.readdirSync(e).forEach((function(r) {
      l(h.join(e, r), t);
     })), r = b ? 100 : 1, n = 0; ;) {
      i = !0;
      try {
       return o = t.rmdirSync(e, t), i = !1, o;
      } finally {
       if (++n < r && i) continue;
      }
     }
    }(e, t);
   }
  }
  var f, h, d, p, m, g, y, b;
  e.exports = i, i.sync = l, f = r(22), h = r(0), d = r(3), p = r(75), m = parseInt("666", 8), 
  g = {
   nosort: !0,
   silent: !0
  }, y = 0, b = "win32" === process.platform;
 }, , , , , , function(e, t, r) {
  var n, i = r(221), o = i("no-color") || i("no-colors") || i("color=false") ? 0 : i("color=16m") || i("color=full") || i("color=truecolor") ? 3 : i("color=256") ? 2 : i("color") || i("colors") || i("color=true") || i("color=always") ? 1 : process.stdout && !process.stdout.isTTY ? 0 : "win32" === process.platform ? 1 : "CI" in process.env ? "TRAVIS" in process.env || "Travis" === process.env.CI ? 1 : 0 : "TEAMCITY_VERSION" in process.env ? null === process.env.TEAMCITY_VERSION.match(/^(9\.(0*[1-9]\d*)\.|\d{2,}\.)/) ? 0 : 1 : /^(screen|xterm)-256(?:color)?/.test(process.env.TERM) ? 2 : /^screen|^xterm|^vt100|color|ansi|cygwin|linux/i.test(process.env.TERM) || "COLORTERM" in process.env ? 1 : (process.env.TERM, 
  0);
  0 === o && "FORCE_COLOR" in process.env && (o = 1), e.exports = process && (0 !== (n = o) && {
   level: n,
   hasBasic: !0,
   has256: n >= 2,
   has16m: n >= 3
  });
 } ]);
}));

class NodeLazyRequire {
 constructor(e, t) {
  this.nodeResolveModule = e, this.lazyDependencies = t, this.ensured = new Set;
 }
 async ensure(e, t) {
  const r = [], n = [];
  if (t.forEach((t => {
   if (!this.ensured.has(t)) {
    const [r, i] = this.lazyDependencies[t];
    try {
     const n = this.nodeResolveModule.resolveModule(e, t);
     if (semiver(JSON.parse(fs__default.default.readFileSync(n, "utf8")).version, r) >= 0) return void this.ensured.add(t);
    } catch (e) {}
    n.push(`${t}@${i}`);
   }
  })), n.length > 0) {
   const e = buildError(r);
   e.header = "Please install missing dev dependencies with either npm or yarn.", e.messageText = `npm install --save-dev ${n.join(" ")}`;
  }
  return r;
 }
 require(e, t) {
  const r = this.getModulePath(e, t);
  return require(r);
 }
 getModulePath(e, t) {
  const r = this.nodeResolveModule.resolveModule(e, t);
  return path__default.default.dirname(r);
 }
}

class NodeResolveModule {
 constructor() {
  this.resolveModuleCache = new Map;
 }
 resolveModule(e, t, r) {
  const n = `${e}:${t}`, i = this.resolveModuleCache.get(n);
  if (i) return i;
  if (r && r.manuallyResolve) return this.resolveModuleManually(e, t, n);
  if (t.startsWith("@types/")) return this.resolveTypesModule(e, t, n);
  const o = require("module");
  e = path__default.default.resolve(e);
  const s = path__default.default.join(e, "noop.js");
  let a = normalizePath(o._resolveFilename(t, {
   id: s,
   filename: s,
   paths: o._nodeModulePaths(e)
  }));
  const c = normalizePath(path__default.default.parse(e).root);
  let l;
  for (;a !== c; ) if (a = normalizePath(path__default.default.dirname(a)), l = path__default.default.join(a, "package.json"), 
  fs__default.default.existsSync(l)) return this.resolveModuleCache.set(n, l), l;
  throw new Error(`error loading "${t}" from "${e}"`);
 }
 resolveTypesModule(e, t, r) {
  const n = t.split("/"), i = normalizePath(path__default.default.parse(e).root);
  let o, s = normalizePath(path__default.default.join(e, "noop.js"));
  for (;s !== i; ) if (s = normalizePath(path__default.default.dirname(s)), o = path__default.default.join(s, "node_modules", n[0], n[1], "package.json"), 
  fs__default.default.existsSync(o)) return this.resolveModuleCache.set(r, o), o;
  throw new Error(`error loading "${t}" from "${e}"`);
 }
 resolveModuleManually(e, t, r) {
  const n = normalizePath(path__default.default.parse(e).root);
  let i, o = normalizePath(path__default.default.join(e, "noop.js"));
  for (;o !== n; ) if (o = normalizePath(path__default.default.dirname(o)), i = path__default.default.join(o, "node_modules", t, "package.json"), 
  fs__default.default.existsSync(i)) return this.resolveModuleCache.set(r, i), i;
  throw new Error(`error loading "${t}" from "${e}"`);
 }
}

class NodeWorkerMain extends require$$7.EventEmitter {
 constructor(e, t) {
  super(), this.id = e, this.tasks = new Map, this.exitCode = null, this.processQueue = !0, 
  this.sendQueue = [], this.stopped = !1, this.successfulMessage = !1, this.totalTasksAssigned = 0, 
  this.fork(t);
 }
 fork(e) {
  const t = {
   execArgv: process.execArgv.filter((e => !/^--(debug|inspect)/.test(e))),
   env: process.env,
   cwd: process.cwd(),
   silent: !0
  };
  this.childProcess = cp__namespace.fork(e, [], t), this.childProcess.stdout.setEncoding("utf8"), 
  this.childProcess.stdout.on("data", (e => {
   console.log(e);
  })), this.childProcess.stderr.setEncoding("utf8"), this.childProcess.stderr.on("data", (e => {
   console.log(e);
  })), this.childProcess.on("message", this.receiveFromWorker.bind(this)), this.childProcess.on("error", (e => {
   this.emit("error", e);
  })), this.childProcess.once("exit", (e => {
   this.exitCode = e, this.emit("exit", e);
  }));
 }
 run(e) {
  this.totalTasksAssigned++, this.tasks.set(e.stencilId, e), this.sendToWorker({
   stencilId: e.stencilId,
   args: e.inputArgs
  });
 }
 sendToWorker(e) {
  this.processQueue ? this.childProcess.send(e, (e => {
   if (!(e && e instanceof Error) && (this.processQueue = !0, this.sendQueue.length > 0)) {
    const e = this.sendQueue.slice();
    this.sendQueue = [], e.forEach((e => this.sendToWorker(e)));
   }
  })) && !/^win/.test(process.platform) || (this.processQueue = !1) : this.sendQueue.push(e);
 }
 receiveFromWorker(e) {
  if (this.successfulMessage = !0, this.stopped) return;
  const t = this.tasks.get(e.stencilId);
  t ? (null != e.stencilRtnError ? t.reject(e.stencilRtnError) : t.resolve(e.stencilRtnValue), 
  this.tasks.delete(e.stencilId), this.emit("response", e)) : null != e.stencilRtnError && this.emit("error", e.stencilRtnError);
 }
 stop() {
  this.stopped = !0, this.tasks.forEach((e => e.reject(TASK_CANCELED_MSG))), this.tasks.clear(), 
  this.successfulMessage ? (this.childProcess.send({
   exit: !0
  }), setTimeout((() => {
   null === this.exitCode && this.childProcess.kill("SIGKILL");
  }), 100)) : this.childProcess.kill("SIGKILL");
 }
}

class NodeWorkerController extends require$$7.EventEmitter {
 constructor(e, t) {
  super(), this.forkModulePath = e, this.workerIds = 0, this.stencilId = 0, this.isEnding = !1, 
  this.taskQueue = [], this.workers = [];
  const r = require$$6.cpus().length;
  this.useForkedWorkers = t > 0, this.maxWorkers = Math.max(Math.min(t, r), 2) - 1, 
  this.useForkedWorkers ? this.startWorkers() : this.mainThreadRunner = require(e);
 }
 onError(e, t) {
  if ("ERR_IPC_CHANNEL_CLOSED" === e.code) return this.stopWorker(t);
  "EPIPE" !== e.code && console.error(e);
 }
 onExit(e) {
  setTimeout((() => {
   let t = !1;
   const r = this.workers.find((t => t.id === e));
   r && (r.tasks.forEach((e => {
    e.retries++, this.taskQueue.unshift(e), t = !0;
   })), r.tasks.clear()), this.stopWorker(e), t && this.processTaskQueue();
  }), 10);
 }
 startWorkers() {
  for (;this.workers.length < this.maxWorkers; ) this.startWorker();
 }
 startWorker() {
  const e = this.workerIds++, t = new NodeWorkerMain(e, this.forkModulePath);
  t.on("response", this.processTaskQueue.bind(this)), t.once("exit", (() => {
   this.onExit(e);
  })), t.on("error", (t => {
   this.onError(t, e);
  })), this.workers.push(t);
 }
 stopWorker(e) {
  const t = this.workers.find((t => t.id === e));
  if (t) {
   t.stop();
   const e = this.workers.indexOf(t);
   e > -1 && this.workers.splice(e, 1);
  }
 }
 processTaskQueue() {
  if (!this.isEnding) for (this.useForkedWorkers && this.startWorkers(); this.taskQueue.length > 0; ) {
   const e = getNextWorker(this.workers);
   if (!e) break;
   e.run(this.taskQueue.shift());
  }
 }
 send(...e) {
  return this.isEnding ? Promise.reject(TASK_CANCELED_MSG) : this.useForkedWorkers ? new Promise(((t, r) => {
   const n = {
    stencilId: this.stencilId++,
    inputArgs: e,
    retries: 0,
    resolve: t,
    reject: r
   };
   this.taskQueue.push(n), this.processTaskQueue();
  })) : this.mainThreadRunner[e[0]].apply(null, e.slice(1));
 }
 handler(e) {
  return (...t) => this.send(e, ...t);
 }
 cancelTasks() {
  for (const e of this.workers) e.tasks.forEach((e => e.reject(TASK_CANCELED_MSG))), 
  e.tasks.clear();
  this.taskQueue.length = 0;
 }
 destroy() {
  if (!this.isEnding) {
   this.isEnding = !0;
   for (const e of this.taskQueue) e.reject(TASK_CANCELED_MSG);
   this.taskQueue.length = 0;
   const e = this.workers.map((e => e.id));
   for (const t of e) this.stopWorker(t);
  }
 }
}

exports.createNodeLogger = e => {
 let t = !0;
 const r = e.process, n = createTerminalLogger({
  color: (e, r) => t ? ansiColors[r](e) : e,
  cwd: () => r.cwd(),
  emoji: e => "win32" !== r.platform ? e : "",
  enableColors: e => t = e,
  getColumns: () => {
   const e = r.stdout && r.stdout.columns || 80;
   return Math.max(Math.min(120, e), 60);
  },
  memoryUsage: () => r.memoryUsage().rss,
  relativePath: (e, t) => path__default.default.relative(e, t),
  writeLogs: (e, t, r) => {
   if (r) try {
    fs__default.default.accessSync(e);
   } catch (e) {
    r = !1;
   }
   r ? fs__default.default.appendFileSync(e, t) : fs__default.default.writeFileSync(e, t);
  }
 });
 return n.createLineUpdater = async () => {
  const e = await Promise.resolve().then((function() {
   return _interopNamespace(require("readline"));
  }));
  let t = Promise.resolve();
  const n = n => (n = n.substr(0, r.stdout.columns - 5) + "[0m", t = t.then((() => new Promise((t => {
   e.clearLine(r.stdout, 0), e.cursorTo(r.stdout, 0, null), r.stdout.write(n, t);
  })))));
  return r.stdout.write("[?25l"), {
   update: n,
   stop: () => n("[?25h")
  };
 }, n;
}, exports.createNodeSys = function createNodeSys(e = {}) {
 const t = e.process || global.process, r = new Set, n = [], i = require$$6.cpus(), o = i.length, s = require$$6.platform(), a = path__default.default.join(__dirname, "..", "..", "compiler", "stencil.js"), c = path__default.default.join(__dirname, "..", "..", "dev-server", "index.js"), l = () => {
  const e = [];
  let t;
  for (;"function" == typeof (t = n.pop()); ) try {
   const n = t();
   (r = n) && ("object" == typeof r || "function" == typeof r) && "function" == typeof r.then && e.push(n);
  } catch (e) {}
  var r;
  return e.length > 0 ? Promise.all(e) : null;
 }, u = {
  name: "node",
  version: t.versions.node,
  access: e => new Promise((t => {
   fs__default.default.access(e, (e => t(!e)));
  })),
  accessSync(e) {
   let t = !1;
   try {
    fs__default.default.accessSync(e), t = !0;
   } catch (e) {}
   return t;
  },
  addDestory(e) {
   r.add(e);
  },
  removeDestory(e) {
   r.delete(e);
  },
  applyPrerenderGlobalPatch(e) {
   if ("function" != typeof global.fetch) {
    const t = require(path__default.default.join(__dirname, "node-fetch.js"));
    global.fetch = (r, n) => {
     if ("string" == typeof r) {
      const i = new URL(r, e.devServerHostUrl).href;
      return t.fetch(i, n);
     }
     return r.url = new URL(r.url, e.devServerHostUrl).href, t.fetch(r, n);
    }, global.Headers = t.Headers, global.Request = t.Request, global.Response = t.Response, 
    global.FetchError = t.FetchError;
   }
   e.window.fetch = global.fetch, e.window.Headers = global.Headers, e.window.Request = global.Request, 
   e.window.Response = global.Response, e.window.FetchError = global.FetchError;
  },
  fetch: (e, t) => {
   const r = require(path__default.default.join(__dirname, "node-fetch.js"));
   if ("string" == typeof e) {
    const n = new URL(e).href;
    return r.fetch(n, t);
   }
   return e.url = new URL(e.url).href, r.fetch(e, t);
  },
  checkVersion,
  copyFile: (e, t) => new Promise((r => {
   fs__default.default.copyFile(e, t, (e => {
    r(!e);
   }));
  })),
  createDir: (e, t) => new Promise((r => {
   t ? fs__default.default.mkdir(e, t, (t => {
    r({
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     newDirs: [],
     error: t
    });
   })) : fs__default.default.mkdir(e, (t => {
    r({
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     newDirs: [],
     error: t
    });
   }));
  })),
  createDirSync(e, t) {
   const r = {
    basename: path__default.default.basename(e),
    dirname: path__default.default.dirname(e),
    path: e,
    newDirs: [],
    error: null
   };
   try {
    fs__default.default.mkdirSync(e, t);
   } catch (e) {
    r.error = e;
   }
   return r;
  },
  createWorkerController(e) {
   const t = path__default.default.join(__dirname, "worker.js");
   return new NodeWorkerController(t, e);
  },
  async destroy() {
   const e = [];
   r.forEach((t => {
    try {
     const r = t();
     r && r.then && e.push(r);
    } catch (e) {
     console.error(`node sys destroy: ${e}`);
    }
   })), e.length > 0 && await Promise.all(e), r.clear();
  },
  dynamicImport: e => Promise.resolve(require(e)),
  encodeToBase64: e => Buffer.from(e).toString("base64"),
  ensureDependencies: async () => ({
   stencilPath: u.getCompilerExecutingPath(),
   diagnostics: []
  }),
  async ensureResources() {},
  exit: async e => {
   await l(), exit(e);
  },
  getCurrentDirectory: () => normalizePath(t.cwd()),
  getCompilerExecutingPath: () => a,
  getDevServerExecutingPath: () => c,
  getEnvironmentVar: e => process.env[e],
  getLocalModulePath: () => null,
  getRemoteModuleUrl: () => null,
  glob: asyncGlob,
  hardwareConcurrency: o,
  isSymbolicLink: e => new Promise((t => {
   try {
    fs__default.default.lstat(e, ((e, r) => {
     t(!e && r.isSymbolicLink());
    }));
   } catch (e) {
    t(!1);
   }
  })),
  nextTick: t.nextTick,
  normalizePath,
  onProcessInterrupt: e => {
   n.includes(e) || n.push(e);
  },
  platformPath: path__default.default,
  readDir: e => new Promise((t => {
   fs__default.default.readdir(e, ((r, n) => {
    t(r ? [] : n.map((t => normalizePath(path__default.default.join(e, t)))));
   }));
  })),
  parseYarnLockFile: e => lockfile.parse(e),
  isTTY() {
   var e;
   return !!(null === (e = null === process || void 0 === process ? void 0 : process.stdout) || void 0 === e ? void 0 : e.isTTY);
  },
  readDirSync(e) {
   try {
    return fs__default.default.readdirSync(e).map((t => normalizePath(path__default.default.join(e, t))));
   } catch (e) {}
   return [];
  },
  readFile: (e, t) => new Promise("binary" === t ? t => {
   fs__default.default.readFile(e, ((e, r) => {
    t(r);
   }));
  } : t => {
   fs__default.default.readFile(e, "utf8", ((e, r) => {
    t(r);
   }));
  }),
  readFileSync(e) {
   try {
    return fs__default.default.readFileSync(e, "utf8");
   } catch (e) {}
  },
  homeDir() {
   try {
    return require$$6__namespace.homedir();
   } catch (e) {}
  },
  realpath: e => new Promise((t => {
   fs__default.default.realpath(e, "utf8", ((e, r) => {
    t({
     path: r,
     error: e
    });
   }));
  })),
  realpathSync(e) {
   const t = {
    path: void 0,
    error: null
   };
   try {
    t.path = fs__default.default.realpathSync(e, "utf8");
   } catch (e) {
    t.error = e;
   }
   return t;
  },
  rename: (e, t) => new Promise((r => {
   fs__default.default.rename(e, t, (n => {
    r({
     oldPath: e,
     newPath: t,
     error: n,
     oldDirs: [],
     oldFiles: [],
     newDirs: [],
     newFiles: [],
     renamed: [],
     isFile: !1,
     isDirectory: !1
    });
   }));
  })),
  resolvePath: e => normalizePath(e),
  removeDir: (e, t) => new Promise((r => {
   t && t.recursive ? fs__default.default.rmdir(e, {
    recursive: !0
   }, (t => {
    r({
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     removedDirs: [],
     removedFiles: [],
     error: t
    });
   })) : fs__default.default.rmdir(e, (t => {
    r({
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     removedDirs: [],
     removedFiles: [],
     error: t
    });
   }));
  })),
  removeDirSync(e, t) {
   try {
    return t && t.recursive ? fs__default.default.rmdirSync(e, {
     recursive: !0
    }) : fs__default.default.rmdirSync(e), {
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     removedDirs: [],
     removedFiles: [],
     error: null
    };
   } catch (t) {
    return {
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     removedDirs: [],
     removedFiles: [],
     error: t
    };
   }
  },
  removeFile: e => new Promise((t => {
   fs__default.default.unlink(e, (r => {
    t({
     basename: path__default.default.basename(e),
     dirname: path__default.default.dirname(e),
     path: e,
     error: r
    });
   }));
  })),
  removeFileSync(e) {
   const t = {
    basename: path__default.default.basename(e),
    dirname: path__default.default.dirname(e),
    path: e,
    error: null
   };
   try {
    fs__default.default.unlinkSync(e);
   } catch (e) {
    t.error = e;
   }
   return t;
  },
  setupCompiler(e) {
   const t = e.ts, r = t.sys.watchDirectory, n = t.sys.watchFile;
   u.watchTimeout = 80, u.events = (() => {
    const e = [], t = t => {
     const r = e.findIndex((e => e.callback === t));
     return r > -1 && (e.splice(r, 1), !0);
    };
    return {
     emit: (t, r) => {
      const n = t.toLowerCase().trim(), i = e.slice();
      for (const e of i) if (null == e.eventName) try {
       e.callback(t, r);
      } catch (e) {
       console.error(e);
      } else if (e.eventName === n) try {
       e.callback(r);
      } catch (e) {
       console.error(e);
      }
     },
     on: (r, n) => {
      if ("function" == typeof r) {
       const n = null, i = r;
       return e.push({
        eventName: n,
        callback: i
       }), () => t(i);
      }
      if ("string" == typeof r && "function" == typeof n) {
       const i = r.toLowerCase().trim(), o = n;
       return e.push({
        eventName: i,
        callback: o
       }), () => t(o);
      }
      return () => !1;
     },
     unsubscribeAll: () => {
      e.length = 0;
     }
    };
   })(), u.watchDirectory = (e, t, n) => {
    const i = r(e, (e => {
     t(normalizePath(e), "fileUpdate");
    }), n), o = () => {
     i.close();
    };
    return u.addDestory(o), {
     close() {
      u.removeDestory(o), i.close();
     }
    };
   }, u.watchFile = (e, r) => {
    const i = n(e, ((e, n) => {
     e = normalizePath(e), n === t.FileWatcherEventKind.Created ? (r(e, "fileAdd"), u.events.emit("fileAdd", e)) : n === t.FileWatcherEventKind.Changed ? (r(e, "fileUpdate"), 
     u.events.emit("fileUpdate", e)) : n === t.FileWatcherEventKind.Deleted && (r(e, "fileDelete"), 
     u.events.emit("fileDelete", e));
    })), o = () => {
     i.close();
    };
    return u.addDestory(o), {
     close() {
      u.removeDestory(o), i.close();
     }
    };
   };
  },
  stat: e => new Promise((t => {
   fs__default.default.stat(e, ((e, r) => {
    t(e ? {
     isDirectory: !1,
     isFile: !1,
     isSymbolicLink: !1,
     size: 0,
     mtimeMs: 0,
     error: e
    } : {
     isDirectory: r.isDirectory(),
     isFile: r.isFile(),
     isSymbolicLink: r.isSymbolicLink(),
     size: r.size,
     mtimeMs: r.mtimeMs,
     error: null
    });
   }));
  })),
  statSync(e) {
   try {
    const t = fs__default.default.statSync(e);
    return {
     isDirectory: t.isDirectory(),
     isFile: t.isFile(),
     isSymbolicLink: t.isSymbolicLink(),
     size: t.size,
     mtimeMs: t.mtimeMs,
     error: null
    };
   } catch (e) {
    return {
     isDirectory: !1,
     isFile: !1,
     isSymbolicLink: !1,
     size: 0,
     mtimeMs: 0,
     error: e
    };
   }
  },
  tmpDirSync: () => require$$6.tmpdir(),
  writeFile: (e, t) => new Promise((r => {
   fs__default.default.writeFile(e, t, (t => {
    r({
     path: e,
     error: t
    });
   }));
  })),
  writeFileSync(e, t) {
   const r = {
    path: e,
    error: null
   };
   try {
    fs__default.default.writeFileSync(e, t);
   } catch (e) {
    r.error = e;
   }
   return r;
  },
  generateContentHash(e, t) {
   let r = require$$3.createHash("sha1").update(e).digest("hex").toLowerCase();
   return "number" == typeof t && (r = r.substr(0, t)), Promise.resolve(r);
  },
  generateFileHash: (e, t) => new Promise(((r, n) => {
   const i = require$$3.createHash("sha1");
   fs__default.default.createReadStream(e).on("error", (e => n(e))).on("data", (e => i.update(e))).on("end", (() => {
    let e = i.digest("hex").toLowerCase();
    "number" == typeof t && (e = e.substr(0, t)), r(e);
   }));
  })),
  copy: nodeCopyTasks,
  details: {
   cpuModel: (Array.isArray(i) && i.length > 0 ? i[0] && i[0].model : "") || "",
   freemem: () => require$$6.freemem(),
   platform: "darwin" === s || "linux" === s ? s : "win32" === s ? "windows" : "",
   release: require$$6.release(),
   totalmem: require$$6.totalmem()
  }
 }, f = new NodeResolveModule;
 return u.lazyRequire = new NodeLazyRequire(f, {
  "@types/jest": [ "24.9.1", "26.0.21" ],
  jest: [ "24.9.0", "26.6.3" ],
  "jest-cli": [ "24.9.0", "26.6.3" ],
  pixelmatch: [ "4.0.2", "4.0.2" ],
  puppeteer: [ "1.19.0", "10.0.0" ],
  "puppeteer-core": [ "1.19.0", "5.2.1" ],
  "workbox-build": [ "4.3.1", "4.3.1" ]
 }), t.on("SIGINT", l), t.on("exit", l), u;
}, exports.setupNodeProcess = function setupNodeProcess(e) {
 e.process.on("unhandledRejection", (t => {
  if (!shouldIgnoreError(t)) {
   let r = "unhandledRejection";
   null != t && ("string" == typeof t ? r += ": " + t : t.stack ? r += ": " + t.stack : t.message && (r += ": " + t.message)), 
   e.logger.error(r);
  }
 }));
};