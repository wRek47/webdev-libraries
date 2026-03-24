// framework los algebra
// Interval + AgenticCursor + Navigator + Polyhedral + CMS

const IS_BROWSER = typeof window !== "undefined" && typeof document !== "undefined";
const DEFAULT_TYPE = "open";
const TYPE_PRIORITY = ["open", "half-falling", "half-rising", "closed"];
const BREADCRUMB_LIMIT = 10;

function finiteNumber(value, fallback = 0) {
  const n = Number(value);
  return Number.isFinite(n) ? n : fallback;
}

function nonNegativeNumber(value, fallback = 0) {
  return Math.max(0, finiteNumber(value, fallback));
}

function makeId() {
  if (IS_BROWSER && typeof crypto !== "undefined" && typeof crypto.randomUUID === "function") {
    return crypto.randomUUID();
  }
  return `id_${Math.random().toString(36).slice(2, 12)}_${Date.now().toString(36)}`;
}

function clampBreadcrumbs(breadcrumbs) {
  if (!Array.isArray(breadcrumbs)) return [];
  return breadcrumbs.slice(-BREADCRUMB_LIMIT);
}

function normalizeType(type) {
  return typeof type === "string" && type.length ? type : DEFAULT_TYPE;
}

function mergeTypes(typeA, typeB) {
  const a = normalizeType(typeA);
  const b = normalizeType(typeB);

  const rankA = TYPE_PRIORITY.indexOf(a);
  const rankB = TYPE_PRIORITY.indexOf(b);
  const safeA = rankA >= 0 ? rankA : 0;
  const safeB = rankB >= 0 ? rankB : 0;

  return TYPE_PRIORITY[Math.max(safeA, safeB)] || DEFAULT_TYPE;
}

function validateSquareMatrix(matrix, size = 4) {
  if (!Array.isArray(matrix) || matrix.length !== size) {
    throw new TypeError(`Matrix must be a ${size}x${size} array.`);
  }
  for (const row of matrix) {
    if (!Array.isArray(row) || row.length !== size) {
      throw new TypeError(`Matrix must be a ${size}x${size} array.`);
    }
  }
  return true;
}

function safeText(value) {
  return value == null ? "" : String(value);
}

function deepClone(value) {
  if (typeof structuredClone === "function") return structuredClone(value);
  return JSON.parse(JSON.stringify(value));
}

function getStorage(key) {
  if (!IS_BROWSER) return null;
  try {
    return window.localStorage;
  } catch {
    return null;
  }
}

class Interval {
  constructor({
    c = 0,
    r = 0,
    k = 0,
    h = 0,
    type = DEFAULT_TYPE,
    breadcrumbs = [],
    id = null,
    version = 0
  } = {}) {
    this.id = id || makeId();
    this.version = Number.isFinite(Number(version)) ? Number(version) : 0;
    this.c = finiteNumber(c, 0);
    this.r = nonNegativeNumber(r, 0);
    this.k = finiteNumber(k, 0);
    this.h = finiteNumber(h, 0);
    this.type = normalizeType(type);
    this.breadcrumbs = clampBreadcrumbs(breadcrumbs);
  }

  clone(overrides = {}) {
    return new Interval({
      id: overrides.id ?? this.id,
      version: overrides.version ?? this.version,
      c: overrides.c ?? this.c,
      r: overrides.r ?? this.r,
      k: overrides.k ?? this.k,
      h: overrides.h ?? this.h,
      type: overrides.type ?? this.type,
      breadcrumbs: overrides.breadcrumbs ?? this.breadcrumbs
    });
  }

  add(other) {
    if (!(other instanceof Interval)) {
      throw new TypeError("Interval.add expects another Interval.");
    }

    return new Interval({
      c: this.c + other.c,
      r: this.r + other.r,
      k: this.k + other.k,
      h: this.h + other.h,
      type: mergeTypes(this.type, other.type),
      breadcrumbs: [...this.breadcrumbs, ...other.breadcrumbs, "add"]
    });
  }

  subtract(other) {
    if (!(other instanceof Interval)) {
      throw new TypeError("Interval.subtract expects another Interval.");
    }

    return new Interval({
      c: this.c - other.c,
      r: nonNegativeNumber(this.r - other.r, 0),
      k: this.k - other.k,
      h: this.h - other.h,
      type: mergeTypes(this.type, other.type),
      breadcrumbs: [...this.breadcrumbs, ...other.breadcrumbs, "subtract"]
    });
  }

  scale(factor) {
    const f = finiteNumber(factor, 0);

    return new Interval({
      c: this.c * f,
      r: this.r * Math.abs(f),
      k: this.k * f,
      h: this.h * f,
      type: this.type,
      breadcrumbs: [...this.breadcrumbs, "scale"]
    });
  }

  fold() {
    const flippedType = this.type.includes("rising")
      ? this.type.replace("rising", "falling")
      : this.type.includes("falling")
        ? this.type.replace("falling", "rising")
        : this.type;

    return new Interval({
      c: -this.c,
      r: this.r,
      k: this.k,
      h: -this.h,
      type: flippedType,
      breadcrumbs: [...this.breadcrumbs].reverse().concat("fold")
    });
  }

  collapse(actionName = "collapse") {
    return new Interval({
      id: this.id,
      version: this.version + 1,
      c: 0,
      r: 0,
      k: 0,
      h: 0,
      type: this.type,
      breadcrumbs: [...this.breadcrumbs, actionName]
    });
  }

  project(matrix, actionName = "project") {
    validateSquareMatrix(matrix, 4);

    const vec = [this.c, this.r, this.k, this.h];
    const result = matrix.map((row) =>
      row.reduce((sum, val, idx) => sum + finiteNumber(val, 0) * vec[idx], 0)
    );

    return new Interval({
      id: this.id,
      version: this.version + 1,
      c: result[0] ?? this.c,
      r: nonNegativeNumber(result[1] ?? this.r, this.r),
      k: result[2] ?? this.k,
      h: result[3] ?? this.h,
      type: this.type,
      breadcrumbs: [...this.breadcrumbs, actionName]
    });
  }

  distanceTo(other) {
    if (!(other instanceof Interval)) {
      throw new TypeError("Interval.distanceTo expects another Interval.");
    }
    return Math.abs(this.c - other.c);
  }

  isOverlapping(other) {
    if (!(other instanceof Interval)) {
      throw new TypeError("Interval.isOverlapping expects another Interval.");
    }
    return this.distanceTo(other) <= (this.r + other.r);
  }

  toJSON() {
    return {
      id: this.id,
      version: this.version,
      c: this.c,
      r: this.r,
      k: this.k,
      h: this.h,
      type: this.type,
      breadcrumbs: [...this.breadcrumbs]
    };
  }

  static fromJSON(json) {
    return new Interval(json || {});
  }

  static aggregate(intervals, modeFn, { pairwise = false, normalize = false } = {}) {
    if (!Array.isArray(intervals) || intervals.length === 0) {
      return new Interval();
    }

    if (pairwise) {
      let acc = new Interval();

      for (let i = 0; i < intervals.length; i++) {
        const a = intervals[i];
        let contribution = new Interval();

        for (let j = 0; j < intervals.length; j++) {
          const b = intervals[j];
          const w = typeof modeFn === "function" ? finiteNumber(modeFn(a, b, i, j), 0) : 1;
          contribution = contribution.add(b.scale(w));
        }

        acc = acc.add(contribution);
      }

      return acc;
    }

    const weighted = intervals.map((interval, idx) => {
      const weight = typeof modeFn === "function" ? finiteNumber(modeFn(interval, idx), 1) : 1;
      return { interval, weight };
    });

    const totalWeight = normalize
      ? weighted.reduce((sum, item) => sum + item.weight, 0)
      : 1;

    return weighted.reduce((acc, { interval, weight }) => {
      const normalizedWeight = normalize
        ? (totalWeight === 0 ? 0 : weight / totalWeight)
        : weight;
      return acc.add(interval.scale(normalizedWeight));
    }, new Interval());
  }

  toString() {
    return `Interval(c=${this.c}, r=${this.r}, k=${this.k}, h=${this.h}, type=${this.type})`;
  }
}

class AgenticCursor {
  constructor(position = 0) {
    this.position = finiteNumber(position, 0);
  }

  setPosition(position) {
    this.position = finiteNumber(position, this.position);
    return this;
  }

  isCaptureZone(interval) {
    if (!(interval instanceof Interval)) {
      throw new TypeError("AgenticCursor.isCaptureZone expects an Interval.");
    }
    return Math.abs(this.position - interval.c) <= interval.r;
  }

  solve(interval) {
    if (!(interval instanceof Interval)) {
      throw new TypeError("AgenticCursor.solve expects an Interval.");
    }
    return interval.collapse("solved");
  }

  projectBranch(interval, intensity) {
    if (!(interval instanceof Interval)) {
      throw new TypeError("AgenticCursor.projectBranch expects an Interval.");
    }

    const i = finiteNumber(intensity, 0);
    return interval.project([
      [1, 0, 0, i],
      [0, 1, 0, 0],
      [0, 0, 1, 0],
      [0, 0, 0, 1]
    ], "branch_projected");
  }

  collapse(interval) {
    if (!(interval instanceof Interval)) {
      throw new TypeError("AgenticCursor.collapse expects an Interval.");
    }
    return interval.collapse("collapsed_by_cursor");
  }
}

class Navigator {
  constructor({ mountNode = null, useHistory = true } = {}) {
    this.routes = new Map();
    this.mountNode = mountNode;
    this.useHistory = useHistory && IS_BROWSER;
    this.cursor = new AgenticCursor(0);
    this.active = null;
    this._popstateHandler = null;
    this._started = false;
  }

  register(path, handler) {
    if (typeof path !== "string" || !path.trim()) {
      throw new TypeError("Navigator.register requires a non-empty path.");
    }
    if (typeof handler !== "function") {
      throw new TypeError("Navigator.register requires a function handler.");
    }
    this.routes.set(path, handler);
    return this;
  }

  unregister(path) {
    this.routes.delete(path);
    return this;
  }

  resolvePath(path) {
    if (typeof path !== "string" || !path.trim()) return "/";
    return path.startsWith("/") ? path : `/${path}`;
  }

  renderOutput(output) {
    if (!this.mountNode) return;

    this.mountNode.innerHTML = "";

    if (output instanceof HTMLElement) {
      this.mountNode.appendChild(output);
      return;
    }

    if (typeof output === "string") {
      this.mountNode.textContent = output;
      return;
    }

    if (output && typeof output === "object" && output.nodeType === 1) {
      this.mountNode.appendChild(output);
      return;
    }

    this.mountNode.textContent = "";
  }

  navigate(path, state = {}, { pushState = true } = {}) {
    const resolved = this.resolvePath(path);
    const handler = this.routes.get(resolved);

    if (!handler) {
      throw new Error(`Route not found: ${resolved}`);
    }

    if (this.active) {
      this.active = this.cursor.solve(this.active);
    }

    this.active = new Interval({
      c: Date.now(),
      r: 1,
      k: 0,
      h: 1,
      type: "open",
      breadcrumbs: ["navigate", resolved]
    });

    const output = handler({
      path: resolved,
      state,
      interval: this.active,
      navigate: this.navigate.bind(this),
      cursor: this.cursor
    });

    this.renderOutput(output);

    if (this.useHistory && pushState && IS_BROWSER) {
      window.history.pushState({ path: resolved, state }, "", resolved);
    }

    return output;
  }

  start(initialPath = null) {
    if (!IS_BROWSER) return this;

    if (this._started) return this;
    this._started = true;

    this._popstateHandler = () => {
      const path = this.resolvePath(window.location.pathname);
      if (this.routes.has(path)) {
        this.navigate(path, window.history.state?.state || {}, { pushState: false });
      }
    };

    window.addEventListener("popstate", this._popstateHandler);

    const path = this.resolvePath(initialPath || window.location.pathname || "/");
    if (this.routes.has(path)) {
      this.navigate(path, window.history.state?.state || {}, { pushState: false });
    }

    return this;
  }

  stop() {
    if (IS_BROWSER && this._popstateHandler) {
      window.removeEventListener("popstate", this._popstateHandler);
      this._popstateHandler = null;
    }
    this._started = false;
    return this;
  }
}

class Polyhedral {
  constructor({
    id = null,
    type = "node",
    interval = new Interval(),
    content = {},
    children = []
  } = {}) {
    this.id = id || makeId();
    this.type = normalizeType(type);
    this.interval = interval instanceof Interval ? interval : new Interval(interval);
    this.content = content && typeof content === "object" ? deepClone(content) : {};
    this.children = Array.isArray(children)
      ? children.map((child) => (child instanceof Polyhedral ? child : new Polyhedral(child)))
      : [];
  }

  clone() {
    return new Polyhedral(this.toJSON());
  }

  addChild(node) {
    const child = node instanceof Polyhedral ? node : new Polyhedral(node);
    this.children.push(child);
    return child;
  }

  removeChild(id) {
    const before = this.children.length;
    this.children = this.children.filter((child) => child.id !== id);
    for (const child of this.children) child.removeChild(id);
    return this.children.length !== before;
  }

  find(id) {
    if (this.id === id) return this;
    for (const child of this.children) {
      const found = child.find(id);
      if (found) return found;
    }
    return null;
  }

  map(fn) {
    if (typeof fn !== "function") {
      throw new TypeError("Polyhedral.map requires a function.");
    }

    const mapped = fn(this);
    const next = mapped instanceof Polyhedral ? mapped.toJSON() : (mapped || {});

    return new Polyhedral({
      ...next,
      id: next.id ?? this.id,
      type: next.type ?? this.type,
      interval: next.interval ?? this.interval,
      content: next.content ?? this.content,
      children: this.children.map((child) => child.map(fn))
    });
  }

  forEach(fn) {
    if (typeof fn !== "function") {
      throw new TypeError("Polyhedral.forEach requires a function.");
    }
    fn(this);
    for (const child of this.children) child.forEach(fn);
  }

  reduce(fn, acc) {
    if (typeof fn !== "function") {
      throw new TypeError("Polyhedral.reduce requires a function.");
    }

    let result = fn(acc, this);
    for (const child of this.children) {
      result = child.reduce(fn, result);
    }
    return result;
  }

  project(matrix, label = "projected") {
    return this.map((node) => ({
      ...node,
      interval: node.interval.project(matrix, label)
    }));
  }

  collapse() {
    return this.map((node) => ({
      ...node,
      interval: node.interval.collapse("collapsed")
    }));
  }

  updateContent(updater) {
    if (typeof updater === "function") {
      this.content = updater(deepClone(this.content));
      return this;
    }

    this.content = updater && typeof updater === "object" ? deepClone(updater) : {};
    return this;
  }

  aggregateIntervals(modeFn, options = {}) {
    return this.reduce((acc, node) => {
      const weight = typeof modeFn === "function" ? finiteNumber(modeFn(node), 1) : 1;
      return acc.add(node.interval.scale(weight));
    }, new Interval());
  }

  toJSON() {
    return {
      id: this.id,
      type: this.type,
      interval: this.interval.toJSON(),
      content: deepClone(this.content),
      children: this.children.map((child) => child.toJSON())
    };
  }

  static fromJSON(json) {
    return new Polyhedral({
      id: json?.id,
      type: json?.type,
      interval: Interval.fromJSON(json?.interval),
      content: json?.content || {},
      children: Array.isArray(json?.children) ? json.children.map(Polyhedral.fromJSON) : []
    });
  }
}

class CMS {
  constructor({ mountNode, mode = "site", storageKey = "ALG3_CMS", navigator = null } = {}) {
    if (IS_BROWSER && !mountNode) {
      throw new Error("CMS requires mountNode in browser environments.");
    }

    this.mode = mode === "admin" ? "admin" : "site";
    this.mountNode = mountNode || null;
    this.storageKey = storageKey;
    this.storage = getStorage(storageKey);
    this.cursor = new AgenticCursor(0);
    this.navigator = navigator instanceof Navigator ? navigator : new Navigator({ mountNode });
    this.store = this.load() || {
      pages: {}
    };
  }

  load() {
    if (!this.storage) return null;

    try {
      const raw = this.storage.getItem(this.storageKey);
      if (!raw) return null;
      const parsed = JSON.parse(raw);
      return this._hydrateStore(parsed);
    } catch {
      return null;
    }
  }

  save() {
    if (!this.storage) return;
    try {
      this.storage.setItem(this.storageKey, JSON.stringify(this._dehydrateStore()));
    } catch {
      // Avoid throwing on quota issues in production; caller can inspect persistence externally.
    }
  }

  _dehydrateStore() {
    const pages = {};
    for (const [path, page] of Object.entries(this.store.pages || {})) {
      pages[path] = {
        interval: page.interval instanceof Interval ? page.interval.toJSON() : Interval.fromJSON(page.interval).toJSON(),
        tree: page.tree instanceof Polyhedral ? page.tree.toJSON() : Polyhedral.fromJSON(page.tree).toJSON()
      };
    }
    return { pages };
  }

  _hydrateStore(parsed) {
    const pages = {};
    for (const [path, page] of Object.entries(parsed?.pages || {})) {
      pages[path] = {
        interval: Interval.fromJSON(page?.interval),
        tree: Polyhedral.fromJSON(page?.tree)
      };
    }
    return { pages };
  }

  listPages() {
    return Object.keys(this.store.pages || {}).sort();
  }

  getPage(path) {
    return this.store.pages[path] || null;
  }

  createPage(path, { title = "New Page", body = "" } = {}) {
    const resolved = this.navigator.resolvePath(path);
    if (this.store.pages[resolved]) {
      throw new Error(`Page already exists: ${resolved}`);
    }

    this.store.pages[resolved] = {
      interval: new Interval({
        c: this.listPages().length,
        r: 1,
        k: 0,
        h: 1,
        breadcrumbs: ["created", resolved]
      }),
      tree: new Polyhedral({
        type: "page",
        content: { title, body },
        children: []
      })
    };

    this.save();
    return this.store.pages[resolved];
  }

  updatePage(path, updater) {
    const resolved = this.navigator.resolvePath(path);
    const page = this.store.pages[resolved];
    if (!page) throw new Error(`Page not found: ${resolved}`);

    const next = typeof updater === "function" ? updater(page) : updater;
    if (!next || typeof next !== "object") {
      throw new TypeError("updatePage requires an object or updater function.");
    }

    if (next.interval) {
      page.interval = next.interval instanceof Interval ? next.interval : new Interval(next.interval);
    }

    if (next.tree) {
      page.tree = next.tree instanceof Polyhedral ? next.tree : Polyhedral.fromJSON(next.tree);
    }

    this.save();
    return page;
  }

  deletePage(path) {
    const resolved = this.navigator.resolvePath(path);
    if (!this.store.pages[resolved]) return false;
    delete this.store.pages[resolved];
    this.save();
    return true;
  }

  renderNode(node) {
    const el = document.createElement("div");
    el.dataset.nodeId = node.id;
    el.dataset.nodeType = node.type;

    const title = node.content?.title;
    const text = node.content?.text ?? node.content?.body;

    if (title) {
      const h = document.createElement("h1");
      h.textContent = safeText(title);
      el.appendChild(h);
    }

    if (text) {
      const p = document.createElement("p");
      p.textContent = safeText(text);
      el.appendChild(p);
    }

    for (const child of node.children) {
      el.appendChild(this.renderNode(child));
    }

    return el;
  }

  renderPage(page) {
    if (!page) return document.createTextNode("Page not found.");
    return this.renderNode(page.tree);
  }

  registerSiteRoutes() {
    for (const path of this.listPages()) {
      this.navigator.register(path, ({ interval }) => {
        const page = this.getPage(path);
        const root = document.createElement("div");
        root.className = "cms-page";
        root.dataset.path = path;
        root.dataset.interval = JSON.stringify(interval.toJSON());

        root.appendChild(this.renderPage(page));
        return root;
      });
    }
    return this;
  }

  initSite(initialPath = null) {
    if (!IS_BROWSER) return this;
    this.registerSiteRoutes();
    this.navigator.start(initialPath);
    return this;
  }

  renderAdminList() {
    const container = document.createElement("div");
    container.className = "cms-admin";

    const header = document.createElement("header");
    const title = document.createElement("h1");
    title.textContent = "CMS Admin";
    header.appendChild(title);

    const createBtn = document.createElement("button");
    createBtn.type = "button";
    createBtn.textContent = "Create Page";
    header.appendChild(createBtn);

    const list = document.createElement("div");
    list.className = "cms-page-list";

    const refresh = () => {
      list.replaceChildren();

      for (const path of this.listPages()) {
        const page = this.getPage(path);
        const row = document.createElement("div");
        row.className = "cms-page-row";

        const label = document.createElement("strong");
        label.textContent = path;

        const meta = document.createElement("span");
        meta.textContent = `  ·  ${page?.interval?.toString?.() || ""}`;

        const editBtn = document.createElement("button");
        editBtn.type = "button";
        editBtn.textContent = "Edit";

        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.textContent = "Delete";

        editBtn.addEventListener("click", () => this.renderAdminEditor(path, refresh));
        deleteBtn.addEventListener("click", () => {
          if (window.confirm(`Delete page ${path}?`)) {
            this.deletePage(path);
            refresh();
          }
        });

        row.append(label, meta, editBtn, deleteBtn);
        list.appendChild(row);
      }
    };

    createBtn.addEventListener("click", () => {
      const path = window.prompt("Enter path (example: /about)");
      if (!path) return;

      try {
        this.createPage(path, { title: "New Page", body: "Edit me." });
        refresh();
      } catch (err) {
        window.alert(err.message);
      }
    });

    container.append(header, list);
    refresh();
    return container;
  }

  renderAdminEditor(path, onBack) {
    const page = this.getPage(path);
    if (!page) throw new Error(`Page not found: ${path}`);

    const container = document.createElement("div");
    container.className = "cms-editor";

    const h = document.createElement("h2");
    h.textContent = `Edit ${path}`;

    const titleInput = document.createElement("input");
    titleInput.type = "text";
    titleInput.placeholder = "Title";
    titleInput.value = safeText(page.tree.content?.title);

    const bodyInput = document.createElement("textarea");
    bodyInput.placeholder = "Body";
    bodyInput.rows = 10;
    bodyInput.value = safeText(page.tree.content?.body ?? page.tree.content?.text);

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.textContent = "Save";

    const backBtn = document.createElement("button");
    backBtn.type = "button";
    backBtn.textContent = "Back";

    saveBtn.addEventListener("click", () => {
      page.tree.updateContent((current) => ({
        ...current,
        title: titleInput.value,
        body: bodyInput.value
      }));

      page.interval = page.interval.project([
        [1, 0, 0, 0],
        [0, 1, 0, 0],
        [0, 0, 1, 0.05],
        [0, 0, 0, 1]
      ], "updated");

      this.save();
      onBack();
    });

    backBtn.addEventListener("click", onBack);

    container.append(h, titleInput, bodyInput, saveBtn, backBtn);
    return container;
  }

  initAdmin() {
    if (!IS_BROWSER) return this;
    if (!this.mountNode) throw new Error("CMS admin requires a mountNode.");
    const render = () => {
      this.mountNode.replaceChildren(this.renderAdminList());
    };
    render();
    return this;
  }

  start(initialPath = null) {
    if (!IS_BROWSER) return this;
    if (this.mode === "admin") {
      this.initAdmin();
    } else {
      this.initSite(initialPath);
    }
    return this;
  }

  destroy() {
    this.navigator.stop();
    return this;
  }
}

export {
  Interval,
  AgenticCursor,
  Navigator,
  Polyhedral,
  CMS
};

export default {
  Interval,
  AgenticCursor,
  Navigator,
  Polyhedral,
  CMS
};