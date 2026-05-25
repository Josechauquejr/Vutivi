const { useEffect, useMemo, useState } = React;

const LOCK_MS = 5 * 60 * 1000;
const STATE_API = "/api/moderation/state";
const USERS_API = "/api/moderation/users";
const ME_API = "/api/moderation/me";
const RESOURCE_API = "/api/moderation/resources";
const BLACKLIST_WORDS_API = "/api/moderation/blacklist/words";
const BLACKLIST_HASHES_API = "/api/moderation/blacklist/hashes";
const currentUser = window.VUTIVI_MODERATION_USER;

const navGroups = [
    {
        label: "Monitorar",
        items: [
            ["dashboard", "Acompanhar", "home-01"],
            ["logs", "Auditar atividade", "activity"],
        ],
    },
    {
        label: "Revisar",
        items: [
            ["queue", "Revisar recursos", "time-quarter"],
            ["resources", "Ver biblioteca", "library"],
        ],
    },
    {
        label: "Bloquear",
        items: [
            ["blacklist", "Gerir hashes", "shield-blocked"],
            ["keywords", "Gerir palavras-chave", "key"],
        ],
    },
    {
        label: "Administrar",
        items: [["users", "Gerir utilizadores", "user-group"]],
    },
];

const visibleNavGroups = () =>
    navGroups
        .map((group) => ({
            ...group,
            items: group.items.filter(
                ([, , , role]) => !role || currentUser?.role === role,
            ),
        }))
        .filter((group) => group.items.length);
const flatNav = () => visibleNavGroups().flatMap((group) => group.items);
const pad = (n) => String(n).padStart(2, "0");
const nowIso = () => new Date().toISOString();
const readableDate = (value) =>
    value ? new Date(value).toLocaleString("pt-PT") : "-";
const makeId = (prefix) =>
    `${prefix}-${Math.random().toString(16).slice(2, 10)}`;
const statusLabel = (status) =>
    ({ approved: "Aprovado", rejected: "Rejeitado", review: "Revisão humana" })[
        status
    ] || status;
const scoreLabel = (score) =>
    score <= 30 ? "Baixo" : score >= 80 ? "Crítico" : "Médio";
const scoreColor = (score) =>
    score <= 30
        ? "var(--color-approved)"
        : score >= 80
          ? "var(--color-rejected)"
          : "var(--color-review)";
const lockRemaining = (lock) =>
    Math.max(0, (lock?.expiresAt || 0) - Date.now());
const mmss = (ms) =>
    `${Math.floor(ms / 60000)}:${pad(Math.floor((ms % 60000) / 1000))}`;
const initials = (name) =>
    (name || "VU")
        .split(" ")
        .map((part) => part[0])
        .slice(0, 2)
        .join("")
        .toUpperCase();
const iconName = (icon) => String(icon || "").replace(/^hugeicons:/, "");
const fileKind = (resource = {}) => {
    const format = String(
        resource.format || resource.media_kind || "",
    ).toLowerCase();
    if (resource.media_kind) return resource.media_kind;
    if (format.includes("pdf")) return "pdf";
    if (["mp4", "mov", "webm", "video"].some((item) => format.includes(item)))
        return "video";
    if (
        ["mp3", "wav", "ogg", "m4a", "audio"].some((item) =>
            format.includes(item),
        )
    )
        return "audio";
    if (format.includes("epub") || format.includes("book")) return "epub";
    if (["doc", "docx"].some((item) => format.includes(item))) return "docx";
    return resource.type === "physical" ? "book" : "file";
};
const fileIcon = (resource) =>
    ({
        pdf: "pdf-01",
        video: "video-01",
        audio: "music-note-01",
        epub: "book-open-01",
        book: "book-open-01",
        docx: "doc-01",
    })[fileKind(resource)] || "doc-01";
const severityIcon = (severity) =>
    ({
        critical: "alert-diamond",
        high: "alert-circle",
        medium: "information-circle",
        low: "checkmark-badge-01",
    })[severity] || "information-circle";
const severityLabel = (severity) =>
    ({ low: "Baixo", medium: "Médio", high: "Alto", critical: "Crítico" })[
        severity
    ] || severity;

function App() {
    const [active, setActive] = useState("dashboard");
    const [resources, setResources] = useState([]);
    const [realUsers, setRealUsers] = useState([]);
    const [locks, setLocks] = useState({});
    const [selectedId, setSelectedId] = useState(null);
    const [logs, setLogs] = useState([]);
    const [tick, setTick] = useState(Date.now());
    const [rejecting, setRejecting] = useState(null);
    const [rejectReason, setRejectReason] = useState("");
    const [syncState, setSyncState] = useState("A atualizar painel");
    const [blacklistWords, setBlacklistWords] = useState([]);
    const [blacklistHashes, setBlacklistHashes] = useState([]);

    const addLog = (entry) => {
        setLogs((current) => [
            {
                id: makeId("log"),
                timestamp: nowIso(),
                admin: entry.admin || currentUser?.name || "Laravel",
                ...entry,
            },
            ...current,
        ]);
    };

    const loadRealState = async () => {
        setSyncState("A atualizar painel");

        try {
            const response = await fetch(STATE_API, {
                headers: { Accept: "application/json" },
            });
            if (!response.ok) throw new Error("state failed");
            const data = await response.json();

            setResources(data.resources || []);
            setRealUsers(data.users || []);
            setBlacklistWords(data.blacklist?.words || []);
            setBlacklistHashes(data.blacklist?.hashes || []);
            setLogs(data.activity || []);
            setSyncState("Painel atualizado");
            addLog({
                action: "Painel atualizado",
                resource: "resources/users",
                result: "approved",
            });
        } catch (error) {
            setSyncState("Não foi possível atualizar o painel");
            addLog({
                action: "Falha ao carregar dados reais",
                resource: "state",
                result: "rejected",
            });
        }
    };

    const addBlacklistWord = async ({ word, category, severity }) => {
        const trimmed = word?.trim();
        if (!trimmed) return null;

        const response = await fetch(BLACKLIST_WORDS_API, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
            },
            body: JSON.stringify({
                word: trimmed,
                category: category?.trim() || "Geral",
                severity: severity || "high",
            }),
        });

        if (!response.ok) {
            throw new Error("Falha ao adicionar palavra à blacklist.");
        }

        const data = await response.json();
        if (!data.ok) {
            throw new Error(
                data.message || "Falha ao adicionar palavra à blacklist.",
            );
        }

        setBlacklistWords((current) => [...current, data.word]);
        return data.word;
    };

    const removeBlacklistWord = async (id) => {
        const response = await fetch(`${BLACKLIST_WORDS_API}/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("Falha ao remover a palavra da blacklist.");
        }

        const data = await response.json();
        if (!data.ok) {
            throw new Error(
                data.message || "Falha ao remover a palavra da blacklist.",
            );
        }

        setBlacklistWords((current) =>
            current.filter((item) => item.id !== id),
        );
        return true;
    };

    const addBlacklistHash = async (hash, reason = "") => {
        const trimmed = hash?.trim();
        if (!trimmed) return null;

        const response = await fetch(BLACKLIST_HASHES_API, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
            },
            body: JSON.stringify({ hash: trimmed, reason }),
        });

        if (!response.ok) {
            throw new Error("Falha ao adicionar hash à blacklist.");
        }

        const data = await response.json();
        if (!data.ok) {
            throw new Error(
                data.message || "Falha ao adicionar hash à blacklist.",
            );
        }

        setBlacklistHashes((current) => [data.hash, ...current]);
        return data.hash;
    };

    const updateBlacklistHash = async (id, hash, reason = "") => {
        const response = await fetch(`${BLACKLIST_HASHES_API}/${id}`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
            },
            body: JSON.stringify({ hash: hash?.trim(), reason }),
        });
        const data = await response.json();
        if (!response.ok || !data.ok) {
            throw new Error(data.message || "Falha ao atualizar hash.");
        }
        setBlacklistHashes((current) =>
            current.map((item) =>
                item.id === data.hash.id ? data.hash : item,
            ),
        );
        return data.hash;
    };

    const removeBlacklistHash = async (id) => {
        const response = await fetch(`${BLACKLIST_HASHES_API}/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("Falha ao remover o hash da blacklist.");
        }

        const data = await response.json();
        if (!data.ok) {
            throw new Error(
                data.message || "Falha ao remover o hash da blacklist.",
            );
        }

        setBlacklistHashes((current) =>
            current.filter((item) => item.id !== id),
        );
        return true;
    };

    const updateBlacklistWord = async (id, payload) => {
        const response = await fetch(`${BLACKLIST_WORDS_API}/${id}`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (!response.ok || !data.ok) {
            throw new Error(data.message || "Falha ao atualizar palavra.");
        }
        setBlacklistWords((current) =>
            current.map((item) =>
                item.id === data.word.id ? data.word : item,
            ),
        );
        return data.word;
    };

    useEffect(() => {
        if (currentUser) loadRealState();
    }, []);

    useEffect(() => {
        const timer = setInterval(() => {
            setTick(Date.now());
            setLocks((current) => {
                const next = { ...current };
                Object.entries(current).forEach(([resourceId, lock]) => {
                    if (lock.expiresAt <= Date.now()) delete next[resourceId];
                });
                return next;
            });
        }, 1000);
        return () => clearInterval(timer);
    }, []);

    useEffect(() => {
        const release = () => releaseMine(selectedId);
        window.addEventListener("beforeunload", release);
        return () => window.removeEventListener("beforeunload", release);
    }, [selectedId, locks]);

    const stats = useMemo(() => {
        const total = resources.length;
        const approved = resources.filter(
            (r) => r.status === "approved",
        ).length;
        const rejected = resources.filter(
            (r) => r.status === "rejected",
        ).length;
        const review = resources.filter((r) => r.status === "review").length;
        const digital = resources.filter((r) => r.type === "digital").length;
        const physical = resources.filter((r) => r.type === "physical").length;
        return {
            total,
            approved,
            rejected,
            review,
            digital,
            physical,
            approvalRate: total ? Math.round((approved / total) * 100) : 0,
        };
    }, [resources]);

    const rejectedHashes = useMemo(
        () =>
            resources
                .filter((item) => item.status === "rejected" && item.hash)
                .map((item) => ({
                    id: item.id,
                    hash: item.hash,
                    motivo: item.motivo || "Rejeitado na moderacao",
                    date: item.createdAt,
                    by: item.moderatedBy || "Moderação",
                    resource: item.title,
                })),
        [resources],
    );

    const selected = resources.find((item) => item.id === selectedId);
    const selectedLock = selected ? locks[selected.id] : null;
    const lockIsMine = selectedLock?.adminId === currentUser?.id;
    const lockBlocksMe =
        selectedLock && !lockIsMine && selectedLock.expiresAt > Date.now();

    const releaseMine = (resourceId) => {
        if (!resourceId || locks[resourceId]?.adminId !== currentUser?.id)
            return;
        setLocks((current) => {
            const next = { ...current };
            delete next[resourceId];
            return next;
        });
    };

    const acquireLock = (resource) => {
        const current = locks[resource.id];
        if (
            current &&
            current.adminId !== currentUser.id &&
            current.expiresAt > Date.now()
        ) {
            setSelectedId(resource.id);
            return;
        }
        if (selectedId && selectedId !== resource.id) releaseMine(selectedId);
        setLocks((currentLocks) => ({
            ...currentLocks,
            [resource.id]: {
                adminId: currentUser.id,
                adminName: currentUser.name,
                expiresAt: Date.now() + LOCK_MS,
            },
        }));
        setSelectedId(resource.id);
        addLog({
            action: "Lock adquirido",
            resource: resource.title,
            result: "review",
        });
    };

    const renewLock = () => {
        if (!selected || !lockIsMine) return;
        setLocks((current) => ({
            ...current,
            [selected.id]: {
                ...current[selected.id],
                expiresAt: Date.now() + LOCK_MS,
            },
        }));
        addLog({
            action: "Lock renovado",
            resource: selected.title,
            result: "review",
        });
    };

    const manualDecision = async (resource, decision, reason = "") => {
        if (!resource || !lockIsMine) return;
        const moderationReason =
            reason ||
            (decision === "approved"
                ? "Aprovado por moderador"
                : "Rejeitado por moderador");

        setResources((items) =>
            items.map((item) =>
                item.id === resource.id
                    ? {
                          ...item,
                          status: decision,
                          moderatedBy: currentUser.name,
                          auto: false,
                          motivo: moderationReason,
                      }
                    : item,
            ),
        );

        try {
            const response = await fetch(`${RESOURCE_API}/${resource.id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    status: decision,
                    score: resource.score,
                    reason: moderationReason,
                    auto: false,
                }),
            });
            const data = await response.json();
            if (!response.ok || !data.ok)
                throw new Error(data.message || "Falha ao gravar moderacao.");
            if (data.deleted) {
                setResources((items) =>
                    items.filter((item) => String(item.id) !== String(data.resource_id)),
                );
            } else {
                setResources((items) =>
                    items.map((item) =>
                        String(item.id) === String(data.resource.id)
                            ? data.resource
                            : item,
                    ),
                );
            }
            if (decision === "rejected" && resource.hash) {
                await addBlacklistHash(resource.hash, moderationReason);
            }

            addLog({
                action:
                    decision === "approved"
                        ? "Aprovacao manual"
                        : "Rejeicao manual",
                resource: resource.title,
                result: decision,
            });
        } catch (error) {
            addLog({
                action: "Falha ao gravar moderacao",
                resource: resource.title,
                result: "rejected",
            });
            loadRealState();
        } finally {
            releaseMine(resource.id);
            setSelectedId(null);
            setRejecting(null);
            setRejectReason("");
        }
    };

    if (!currentUser) {
        return <LoginRequired />;
    }

    return (
        <div className="app-shell">
            <AppStyles />
            <aside className="sidebar">
                <div className="brand">
                    <picture>
                        <source
                            srcSet="/img/png/logo_bb.png"
                            media="(prefers-color-scheme: dark)"
                        />
                        <img
                            className="brand-logo"
                            src="/img/png/logo_wb.png"
                            alt="Vutivi"
                        />
                    </picture>
                    <div>
                        <strong>+ Mod</strong>
                        <span>Moderação da Vutivi</span>
                    </div>
                </div>
                <div className="admin-card">
                    <div className="avatar">{initials(currentUser.name)}</div>
                    <div>
                        <strong>{currentUser.name}</strong>
                        <span>
                            {currentUser.role || "admin"} - {currentUser.email}
                        </span>
                    </div>
                </div>
                <nav>
                    {visibleNavGroups().map((group) => (
                        <div className="nav-group" key={group.label}>
                            <small>{group.label}</small>
                            {group.items.map(([key, label, icon]) => (
                                <button
                                    key={key}
                                    className={active === key ? "active" : ""}
                                    onClick={() => setActive(key)}
                                >
                                    <span>
                                        <Icon icon={icon} /> {label}
                                    </span>
                                    {key === "queue" && stats.review > 0 && (
                                        <b className="number">{stats.review}</b>
                                    )}
                                </button>
                            ))}
                        </div>
                    ))}
                </nav>
                <a className="logout" href="/logout">
                    <Icon icon="logout-01" /> Sair
                </a>
            </aside>

            <main className="main">
                <header className="topbar">
                    <div>
                        <p>
                            Gestão de conteúdo e segurança da biblioteca
                        </p>
                        <h1>
                            {flatNav().find(([key]) => key === active)?.[1]}
                        </h1>
                        <small>{syncState}</small>
                    </div>
                    <button className="ghost" onClick={loadRealState}>
                        <Icon icon="refresh" /> Atualizar
                    </button>
                </header>

                {active === "dashboard" && (
                    <Dashboard
                        stats={stats}
                        resources={resources}
                        logs={logs}
                        rejectedHashes={rejectedHashes}
                        realUsers={realUsers}
                        reload={loadRealState}
                    />
                )}
                {active === "guide" && <Guide />}
                {active === "logs" && (
                    <Logs logs={logs} currentUser={currentUser} />
                )}
                {active === "resources" && (
                    <ResourcesTable resources={resources} />
                )}
                {active === "users" && (
                    <UsersPanel
                        users={realUsers}
                        setUsers={setRealUsers}
                        addLog={addLog}
                        reload={loadRealState}
                        currentUser={currentUser}
                    />
                )}
                {active === "queue" && (
                    <ModerationQueue
                        resources={resources}
                        locks={locks}
                        tick={tick}
                        selected={selected}
                        selectedLock={selectedLock}
                        lockIsMine={lockIsMine}
                        lockBlocksMe={lockBlocksMe}
                        acquireLock={acquireLock}
                        renewLock={renewLock}
                        approve={() => manualDecision(selected, "approved")}
                        reject={() => setRejecting(selected)}
                    />
                )}
                {active === "blacklist" && (
                    <RejectedHashes
                        hashes={rejectedHashes}
                        blacklistHashes={blacklistHashes}
                        resources={resources}
                        onAddHash={addBlacklistHash}
                        onUpdateHash={updateBlacklistHash}
                        onRemoveHash={removeBlacklistHash}
                    />
                )}
                {active === "keywords" && (
                    <Keywords
                        blacklistWords={blacklistWords}
                        resources={resources}
                        onAddWord={addBlacklistWord}
                        onUpdateWord={updateBlacklistWord}
                        onRemoveWord={removeBlacklistWord}
                    />
                )}
            </main>

            {rejecting && (
                <div
                    className="modal-backdrop"
                    onMouseDown={() => setRejecting(null)}
                >
                    <div
                        className="modal"
                        onMouseDown={(event) => event.stopPropagation()}
                    >
                        <div className="modal-title">
                            <Icon icon="cancel-circle" />
                            <div>
                                <h2>Rejeitar recurso</h2>
                                <p>Informe o motivo para gravar a decisao.</p>
                            </div>
                        </div>
                        <p>{rejecting.title}</p>
                        <textarea
                            value={rejectReason}
                            onChange={(event) =>
                                setRejectReason(event.target.value)
                            }
                            placeholder="Motivo obrigatorio"
                        />
                        <div className="modal-actions">
                            <button
                                className="ghost"
                                onClick={() => setRejecting(null)}
                            >
                                <Icon icon="cancel-01" /> Cancelar
                            </button>
                            <button
                                className="danger"
                                disabled={!rejectReason.trim()}
                                onClick={() =>
                                    manualDecision(
                                        rejecting,
                                        "rejected",
                                        rejectReason.trim(),
                                    )
                                }
                            >
                                <Icon icon="cancel-circle" /> Rejeitar recurso
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

function LoginRequired() {
    return (
        <div className="login-page">
            <AppStyles />
            <div className="login-panel">
                <Icon icon="lock-01" big />
                <h1>Sessao necessaria</h1>
                <p>
                    Entre com uma conta real da Vutivi para abrir o painel de
                    moderacao.
                </p>
                <a className="primary-link" href="/login">
                    Entrar
                </a>
            </div>
        </div>
    );
}

function Dashboard({
    stats,
    resources,
    logs,
    rejectedHashes,
    realUsers,
    reload,
}) {
    const days = Array.from({ length: 6 }, (_, index) => {
        const date = new Date();
        date.setDate(date.getDate() - (5 - index));
        const day = date.toISOString().slice(0, 10);
        const inDay = resources.filter(
            (r) => (r.createdAt || "").slice(0, 10) === day,
        );
        return {
            label: `${pad(date.getDate())}/${pad(date.getMonth() + 1)}`,
            approved: inDay.filter((r) => r.status === "approved").length,
            review: inDay.filter((r) => r.status === "review").length,
            rejected: inDay.filter((r) => r.status === "rejected").length,
        };
    });
    const maxDay = Math.max(
        1,
        ...days.flatMap((day) => [day.approved, day.review, day.rejected]),
    );

    return (
        <section className="grid-section">
            <div className="cards six">
                <Metric label="Total de recursos" value={stats.total} />
                <Metric label="Na fila" value={stats.review} tone="warn" />
                <Metric label="Aprovados" value={stats.approved} tone="ok" />
                <Metric label="Rejeitados" value={stats.rejected} tone="bad" />
                <Metric label="Digitais" value={stats.digital} />
                <Metric label="Fisicos" value={stats.physical} />
            </div>
            <div className="panel wide">
                <h2>Entradas dos ultimos 6 dias</h2>
                <div className="chart">
                    {days.map((day) => (
                        <div className="bar-group" key={day.label}>
                            <div className="bars">
                                <i
                                    style={{
                                        height: `${Math.max(4, (day.approved / maxDay) * 150)}px`,
                                        background: "var(--color-approved)",
                                    }}
                                />
                                <i
                                    style={{
                                        height: `${Math.max(4, (day.review / maxDay) * 150)}px`,
                                        background: "var(--color-review)",
                                    }}
                                />
                                <i
                                    style={{
                                        height: `${Math.max(4, (day.rejected / maxDay) * 150)}px`,
                                        background: "var(--color-rejected)",
                                    }}
                                />
                            </div>
                            <span>{day.label}</span>
                        </div>
                    ))}
                </div>
            </div>
            <div className="panel">
                <h2>Resumo</h2>
                <div className="summary-row">
                    <span>Hashes rejeitados</span>
                    <b>{rejectedHashes.length}</b>
                </div>
                <div className="summary-row">
                    <span>Taxa de aprovacao</span>
                    <b>{stats.approvalRate}%</b>
                </div>
                <div className="summary-row">
                    <span>Utilizadores reais</span>
                    <b>{realUsers.length}</b>
                </div>
                <button className="ghost full" onClick={reload}>
                    <Icon icon="refresh" /> Atualizar
                </button>
            </div>
            <div className="panel feed">
                <h2>Actividade desta sessao</h2>
                {logs.length ? (
                    logs
                        .slice(0, 8)
                        .map((log) => <LogLine key={log.id} log={log} />)
                ) : (
                    <EmptyState text="As accoes reais desta sessao aparecem aqui." />
                )}
            </div>
        </section>
    );
}

function Guide() {
    const steps = [
        [
            "Cadastro",
            "O utilizador adiciona um recurso em Biblioteca > Adicionar recurso. O formulario grava nas tabelas reais resources, physical_resources ou digital_resources.",
        ],
        [
            "Entrada na moderacao",
            "O painel consulta /api/moderation/state e mostra exactamente os recursos existentes na base de dados.",
        ],
        [
            "Score e sinais",
            "O backend calcula score, estado e palavras sensiveis a partir do titulo, descricao e hash real do ficheiro digital quando existir.",
        ],
        [
            "Revisao humana",
            "Recursos em Revisao humana entram na fila. O moderador bloqueia o item por 5 minutos, aprova ou rejeita, e a decisao e gravada no proprio recurso.",
        ],
        [
            "Biblioteca",
            "Depois da decisao, a biblioteca continua a usar o mesmo registro. Nao ha ficheiros ou utilizadores inventados no painel.",
        ],
    ];

    return (
        <div className="guide-grid">
            <div className="panel guide-main">
                <h2>Como a moderacao interage com a biblioteca</h2>
                <p>
                    A aplicacao esta ligada ao cadastro de recursos da Vutivi.
                    Quando alguem adiciona um recurso fisico ou digital, esse
                    registro passa a estar disponivel para consulta e decisao no
                    painel de moderacao.
                </p>
                <div className="flow">
                    {steps.map(([title, text], index) => (
                        <div className="flow-step" key={title}>
                            <b>{index + 1}</b>
                            <div>
                                <strong>{title}</strong>
                                <span>{text}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
            <div className="panel">
                <h2>Contrato real</h2>
                <div className="summary-row">
                    <span>Lista de dados</span>
                    <b>/api/moderation/state</b>
                </div>
                <div className="summary-row">
                    <span>Decisao</span>
                    <b>PATCH /api/moderation/resources/id</b>
                </div>
                <div className="summary-row">
                    <span>Criar utilizador</span>
                    <b>POST /api/moderation/users</b>
                </div>
                <p className="muted block">
                    A pagina de simulacao de upload foi removida para evitar
                    dados falsos. O caminho correcto e cadastrar/adicionar
                    recurso na biblioteca.
                </p>
            </div>
        </div>
    );
}

function UsersPanel({ users, setUsers, addLog, reload, currentUser }) {
    const [open, setOpen] = useState(false);
    const [confirming, setConfirming] = useState(false);
    const [report, setReport] = useState(null);
    const [busy, setBusy] = useState(false);
    const [editing, setEditing] = useState(null);
    const [accountOpen, setAccountOpen] = useState(false);
    const [photo, setPhoto] = useState(null);
    const [form, setForm] = useState({
        name: "",
        username: "",
        email: "",
        password: "",
        role: "admin",
    });
    const adminUsers = users;

    const reset = () => {
        setOpen(false);
        setAccountOpen(false);
        setConfirming(false);
        setReport(null);
        setBusy(false);
        setEditing(null);
        setPhoto(null);
        setForm({
            name: "",
            username: "",
            email: "",
            password: "",
            role: "admin",
        });
    };

    const openNew = () => {
        setEditing(null);
        setForm({
            name: "",
            username: "",
            email: "",
            password: "",
            role: "admin",
        });
        setOpen(true);
    };

    const openEdit = (user) => {
        setEditing(user);
        setForm({
            name: user.name || "",
            username: user.username || "",
            email: user.email || "",
            password: "",
            role: user.role || "user",
        });
        setConfirming(false);
        setReport(null);
        setOpen(true);
    };

    const openAccount = () => {
        const me =
            users.find((user) => user.id === currentUser?.id) || currentUser;
        setEditing({ ...me, self: true });
        setForm({
            name: me?.name || "",
            username: me?.username || "",
            email: me?.email || "",
            password: "",
            role: me?.role || "admin",
        });
        setPhoto(null);
        setConfirming(false);
        setReport(null);
        setAccountOpen(true);
    };

    const submit = async () => {
        setBusy(true);
        setReport(null);

        if (editing?.self) {
            const payload = new FormData();
            payload.append("name", form.name);
            payload.append("username", form.username || "");
            payload.append("email", form.email);
            if (form.password) payload.append("password", form.password);
            if (photo) payload.append("profile_photo", photo);

            try {
                const response = await fetch(ME_API, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                        Accept: "application/json",
                    },
                    body: payload,
                });
                const data = await response.json();
                if (!response.ok || !data.ok)
                    throw new Error(
                        data.message || "Nao foi possivel atualizar a conta.",
                    );
                setUsers((items) =>
                    items.map((item) =>
                        item.id === data.user.id ? data.user : item,
                    ),
                );
                setReport({
                    ok: true,
                    message: data.message,
                    details: data.user,
                });
                addLog({
                    action: "Conta admin atualizada",
                    resource: data.user.email,
                    result: "approved",
                });
                reload();
            } catch (error) {
                setReport({ ok: false, message: error.message });
            } finally {
                setBusy(false);
                setConfirming(false);
            }
            return;
        }

        if (editing) {
            try {
                const response = await fetch(`${USERS_API}/${editing.id}`, {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN":
                            document.querySelector('meta[name="csrf-token"]')
                                ?.content || "",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(form),
                });
                const data = await response.json();
                if (!response.ok || !data.ok)
                    throw new Error(
                        data.message ||
                            "Nao foi possivel atualizar o administrador.",
                    );
                setUsers((items) =>
                    items.map((item) =>
                        item.id === data.user.id ? data.user : item,
                    ),
                );
                setReport({
                    ok: true,
                    message: data.message,
                    details: data.user,
                });
                addLog({
                    action: "Administrador editado",
                    resource: data.user.email,
                    result: "approved",
                });
                reload();
            } catch (error) {
                setReport({ ok: false, message: error.message });
                addLog({
                    action: "Falha ao editar administrador",
                    resource: form.email,
                    result: "rejected",
                });
            } finally {
                setBusy(false);
                setConfirming(false);
            }
            return;
        }

        try {
            const response = await fetch(USERS_API, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "application/json",
                },
                body: JSON.stringify(form),
            });
            const data = await response.json();
            if (!response.ok || !data.ok)
                throw new Error(
                    data.message ||
                        Object.values(data.errors || {})?.flat()?.[0] ||
                        "Nao foi possivel criar o administrador.",
                );

            setUsers((items) => [data.user, ...items]);
            setReport({ ok: true, message: data.message, details: data.user });
            addLog({
                action: "Administrador criado",
                resource: data.user.email,
                result: "approved",
            });
            reload();
        } catch (error) {
            setReport({ ok: false, message: error.message });
            addLog({
                action: "Falha ao criar administrador",
                resource: form.email,
                result: "rejected",
            });
        } finally {
            setBusy(false);
            setConfirming(false);
        }
    };

    const removeAdmin = async (user) => {
        if (!window.confirm(`Remover o administrador ${user.name}?`)) return;
        setBusy(true);
        try {
            const response = await fetch(`${USERS_API}/${user.id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "application/json",
                },
            });
            const data = await response.json();
            if (!response.ok || !data.ok)
                throw new Error(
                    data.message || "Nao foi possivel remover o administrador.",
                );
            setUsers((items) => items.filter((item) => item.id !== user.id));
            addLog({
                action: "Administrador removido",
                resource: user.email,
                result: "rejected",
            });
        } catch (error) {
            setReport({ ok: false, message: error.message });
        } finally {
            setBusy(false);
        }
    };

    const banUser = async (user) => {
        const reason = window.prompt(
            `Motivo para suspender ${user.name}:`,
            "Submissao de ficheiro ilicito ou conduta imprudente.",
        );
        if (reason === null) return;
        setBusy(true);
        try {
            const response = await fetch(`${USERS_API}/${user.id}/ban`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                    Accept: "application/json",
                },
                body: JSON.stringify({ reason }),
            });
            const data = await response.json();
            if (!response.ok || !data.ok)
                throw new Error(data.message || "Nao foi possivel suspender o utilizador.");
            setUsers((items) =>
                items.map((item) => (item.id === data.user.id ? data.user : item)),
            );
            addLog({
                action: "Utilizador suspenso",
                resource: user.email,
                result: "rejected",
            });
        } catch (error) {
            setReport({ ok: false, message: error.message });
        } finally {
            setBusy(false);
        }
    };

    return (
        <div className="panel">
            <div className="section-head">
                <div>
                    <h2>Utilizadores da Biblioteca</h2>
                    <p>Gerencie administradores e suspenda contas com conduta imprudente.</p>
                </div>
                <div className="actions inline">
                    <button className="ghost" onClick={openAccount}>
                        <Icon icon="user-check-01" /> Minha conta
                    </button>
                    <button onClick={openNew}>
                        <Icon icon="add-circle" /> Novo Admin
                    </button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Estado</th>
                        <th>Recursos</th>
                        <th>Criado em</th>
                        <th>Accoes</th>
                    </tr>
                </thead>
                <tbody>
                    {adminUsers.map((item) => (
                        <tr key={item.id}>
                            <td>
                                <span className="admin-name">
                                    {item.profile_photo_url ? (
                                        <img
                                            src={item.profile_photo_url}
                                            alt=""
                                        />
                                    ) : (
                                        <span className="avatar mini">
                                            {initials(item.name)}
                                        </span>
                                    )}
                                    {item.name}
                                </span>
                            </td>
                            <td>{item.username}</td>
                            <td>{item.email}</td>
                            <td>{item.role || "user"}</td>
                            <td>{item.banned_at ? "Suspenso" : "Ativo"}</td>
                            <td>{item.resources_count}</td>
                            <td>{readableDate(item.created_at)}</td>
                            <td>
                                <div className="table-actions">
                                    <button
                                        className="ghost small"
                                        onClick={() => openEdit(item)}
                                    >
                                        <Icon icon="edit-01" /> Editar
                                    </button>
                                    {item.id !== currentUser?.id && (
                                        <button
                                            className="ghost small danger-text"
                                            onClick={() => banUser(item)}
                                        >
                                            <Icon icon="shield-blocked" /> Suspender
                                        </button>
                                    )}
                                    {item.id !== currentUser?.id && item.role !== "user" && (
                                        <button
                                            className="ghost small danger-text"
                                            onClick={() => removeAdmin(item)}
                                        >
                                            <Icon icon="delete-01" /> Apagar
                                        </button>
                                    )}
                                </div>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {(open || accountOpen) && (
                <div className="modal-backdrop" onMouseDown={reset}>
                    <div
                        className="modal user-modal"
                        onMouseDown={(event) => event.stopPropagation()}
                    >
                        <div className="modal-title">
                            <Icon
                                icon={
                                    accountOpen
                                        ? "user-check-01"
                                        : editing
                                          ? "edit-01"
                                          : "add-circle"
                                }
                            />
                            <div>
                                <h2>
                                    {accountOpen
                                        ? "Gestao da minha conta"
                                        : editing
                                          ? "Editar Admin"
                                          : "Novo Admin"}
                                </h2>
                                <p>Dados administrativos da Vutivi + Mod.</p>
                            </div>
                        </div>

                        {!confirming && !report && (
                            <div className="form-grid">
                                <label>
                                    Nome
                                    <input
                                        value={form.name}
                                        onChange={(event) =>
                                            setForm({
                                                ...form,
                                                name: event.target.value,
                                            })
                                        }
                                    />
                                </label>
                                <label>
                                    Username
                                    <input
                                        value={form.username}
                                        onChange={(event) =>
                                            setForm({
                                                ...form,
                                                username: event.target.value,
                                            })
                                        }
                                        placeholder="Opcional"
                                    />
                                </label>
                                <label>
                                    Email
                                    <input
                                        value={form.email}
                                        onChange={(event) =>
                                            setForm({
                                                ...form,
                                                email: event.target.value,
                                            })
                                        }
                                    />
                                </label>
                                <label>
                                    Password
                                    <input
                                        type="password"
                                        value={form.password}
                                        onChange={(event) =>
                                            setForm({
                                                ...form,
                                                password: event.target.value,
                                            })
                                        }
                                    />
                                </label>
                                {!accountOpen && (
                                    <label>
                                        Role
                                        <select
                                            value={form.role}
                                            onChange={(event) =>
                                                setForm({
                                                    ...form,
                                                    role: event.target.value,
                                                })
                                            }
                                        >
                                            <option value="admin">Admin</option>
                                            <option value="superadmin">
                                                Superadmin
                                            </option>
                                        </select>
                                    </label>
                                )}
                                {accountOpen && (
                                    <label>
                                        Foto de perfil
                                        <input
                                            type="file"
                                            accept="image/*"
                                            onChange={(event) =>
                                                setPhoto(
                                                    event.target.files?.[0] ||
                                                        null,
                                                )
                                            }
                                        />
                                    </label>
                                )}
                                <div className="modal-actions">
                                    <button className="ghost" onClick={reset}>
                                        <Icon icon="cancel-01" /> Cancelar
                                    </button>
                                    <button
                                        disabled={
                                            !form.name ||
                                            !form.email ||
                                            (!editing &&
                                                form.password.length < 6)
                                        }
                                        onClick={() => setConfirming(true)}
                                    >
                                        <Icon icon="sent" /> Continuar
                                    </button>
                                </div>
                            </div>
                        )}

                        {confirming && (
                            <div className="confirm-box">
                                <h3>
                                    {editing
                                        ? "Tem certeza que quer atualizar esta conta?"
                                        : "Tem certeza que quer criar este administrador?"}
                                </h3>
                                <p>
                                    <b>{form.name}</b> - {form.email}
                                </p>
                                <div className="modal-actions">
                                    <button
                                        className="ghost"
                                        onClick={() => setConfirming(false)}
                                    >
                                        <Icon icon="cancel-01" /> Voltar
                                    </button>
                                    <button disabled={busy} onClick={submit}>
                                        <Icon
                                            icon={editing ? "edit-01" : "sent"}
                                        />
                                        {busy
                                            ? "A guardar..."
                                            : editing
                                              ? "Sim, atualizar"
                                              : "Sim, criar"}
                                    </button>
                                </div>
                            </div>
                        )}

                        {report && (
                            <div
                                className={
                                    report.ok ? "report ok" : "report bad"
                                }
                            >
                                <h3>{report.ok ? "Sucesso" : "Falha"}</h3>
                                <p>{report.message}</p>
                                {report.details && (
                                    <pre>
                                        {JSON.stringify(
                                            report.details,
                                            null,
                                            2,
                                        )}
                                    </pre>
                                )}
                                <div className="modal-actions">
                                    <button onClick={reset}>
                                        <Icon icon="cancel-01" /> Fechar
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

function Logs({ logs, currentUser }) {
    const [filter, setFilter] = useState({ action: "", date: "" });
    const filtered = logs.filter(
        (log) =>
            (!filter.action ||
                log.action
                    .toLowerCase()
                    .includes(filter.action.toLowerCase())) &&
            (!filter.date || log.timestamp.slice(0, 10) === filter.date),
    );
    return (
        <div className="panel">
            <div className="filters">
                <input value={currentUser.name} readOnly />
                <input
                    placeholder="Tipo de accao"
                    value={filter.action}
                    onChange={(e) =>
                        setFilter({ ...filter, action: e.target.value })
                    }
                />
                <input
                    type="date"
                    value={filter.date}
                    onChange={(e) =>
                        setFilter({ ...filter, date: e.target.value })
                    }
                />
            </div>
            {filtered.length ? (
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Utilizador</th>
                            <th>Accao</th>
                            <th>Recurso</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {filtered.map((log) => (
                            <tr key={log.id}>
                                <td>{readableDate(log.timestamp)}</td>
                                <td>{log.admin}</td>
                                <td>{log.action}</td>
                                <td>{log.resource}</td>
                                <td>
                                    <StatusBadge status={log.result} />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            ) : (
                <EmptyState text="Sem accoes nesta sessao." />
            )}
        </div>
    );
}

function ResourcesTable({ resources }) {
    const [filter, setFilter] = useState({ status: "", type: "", q: "" });
    const [reader, setReader] = useState(null);
    const filtered = resources.filter(
        (r) =>
            (!filter.status || r.status === filter.status) &&
            (!filter.type || r.type === filter.type) &&
            (!filter.q ||
                r.title.toLowerCase().includes(filter.q.toLowerCase())),
    );
    const types = [...new Set(resources.map((r) => r.type).filter(Boolean))];
    return (
        <div className="panel">
            <div className="filters">
                <label className="search-field">
                    <Icon icon="search-01" />
                    <input
                        placeholder="Pesquisar titulo"
                        value={filter.q}
                        onChange={(e) =>
                            setFilter({ ...filter, q: e.target.value })
                        }
                    />
                </label>
                <Icon icon="filter" />
                <select
                    value={filter.status}
                    onChange={(e) =>
                        setFilter({ ...filter, status: e.target.value })
                    }
                >
                    <option value="">Todos status</option>
                    <option value="review">Revisao</option>
                    <option value="approved">Aprovado</option>
                    <option value="rejected">Rejeitado</option>
                </select>
                <select
                    value={filter.type}
                    onChange={(e) =>
                        setFilter({ ...filter, type: e.target.value })
                    }
                >
                    <option value="">Todos tipos</option>
                    {types.map((type) => (
                        <option key={type}>{type}</option>
                    ))}
                </select>
            </div>
            {filtered.length ? (
                <table>
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Tipo</th>
                            <th>Dono</th>
                            <th>Tamanho</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Moderado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        {filtered.map((r) => (
                            <tr
                                key={r.id}
                                className="clickable-row"
                                onClick={() => setReader(r)}
                            >
                                <td>
                                    <span className="file-title">
                                        <Icon icon={fileIcon(r)} />
                                        {r.title}
                                    </span>
                                </td>
                                <td>{r.format || r.type}</td>
                                <td>{r.uploader}</td>
                                <td>{r.size}</td>
                                <td>
                                    <ScorePill score={r.score} />
                                </td>
                                <td>
                                    <StatusBadge status={r.status} />
                                </td>
                                <td>{readableDate(r.createdAt)}</td>
                                <td>{r.moderatedBy || "-"}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            ) : (
                <EmptyState text="Nenhum recurso real encontrado para este filtro." />
            )}
            {reader && (
                <div
                    className="modal-backdrop"
                    onMouseDown={() => setReader(null)}
                >
                    <div
                        className="modal reader-modal"
                        onMouseDown={(event) => event.stopPropagation()}
                    >
                        <div className="modal-title">
                            <Icon icon={fileIcon(reader)} />
                            <div>
                                <h2>{reader.title}</h2>
                                <p>
                                    {reader.type} -{" "}
                                    {reader.uploader || "Sem uploader"}
                                </p>
                            </div>
                        </div>
                        <FileReader resource={reader} large />
                        <div className="modal-actions">
                            <button
                                className="ghost"
                                onClick={() => setReader(null)}
                            >
                                <Icon icon="cancel-01" /> Fechar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

function ModerationQueue({
    resources,
    locks,
    tick,
    selected,
    selectedLock,
    lockIsMine,
    lockBlocksMe,
    acquireLock,
    renewLock,
    approve,
    reject,
}) {
    const queue = resources.filter((item) =>
        ["review", "approved"].includes(item.status),
    );
    return (
        <div className="queue-layout">
            <div className="panel">
                <h2>Fila de Moderacao</h2>
                <div className="queue-list">
                    {queue.length ? (
                        queue.map((resource) => {
                            const lock = locks[resource.id];
                            const locked = lock && lock.expiresAt > Date.now();
                            return (
                                <button
                                    key={resource.id}
                                    className={`queue-item ${selected?.id === resource.id ? "active" : ""}`}
                                    onClick={() => acquireLock(resource)}
                                >
                                    <strong>{resource.title}</strong>
                                    <span>
                                        {resource.type} - {resource.uploader} -{" "}
                                        {resource.size}
                                    </span>
                                    <div>
                                        <ScorePill score={resource.score} />{" "}
                                        <small>
                                            na fila:{" "}
                                            {Math.max(
                                                1,
                                                Math.floor(
                                                    (Date.now() -
                                                        new Date(
                                                            resource.createdAt,
                                                        ).getTime()) /
                                                        60000,
                                                ),
                                            )}{" "}
                                            min
                                        </small>
                                    </div>
                                    {locked && (
                                        <em>
                                            <Icon icon="lock-01" />
                                            Bloqueado por {lock.adminName} -
                                            expira em{" "}
                                            <span className="number">
                                                {mmss(lockRemaining(lock))}
                                            </span>
                                        </em>
                                    )}
                                    {!locked && (
                                        <em>
                                            <Icon icon="unlock-01" /> Livre
                                        </em>
                                    )}
                                </button>
                            );
                        })
                    ) : (
                        <EmptyState text="Nao ha recursos reais em revisao humana." />
                    )}
                </div>
            </div>
            <div className="panel detail">
                {!selected ? (
                    <EmptyState text="Selecione um recurso para iniciar a revisao." />
                ) : (
                    <>
                        <div className="detail-head">
                            <div>
                                <h2>{selected.title}</h2>
                                <div className="detail-meta">
                                    <span>
                                        <Icon icon={fileIcon(selected)} />{" "}
                                        {selected.format || selected.type}
                                    </span>
                                    <span>{selected.uploader}</span>
                                </div>
                                <p>{selected.description}</p>
                            </div>
                            {selectedLock && (
                                <div className="lock-timer">
                                    <Icon
                                        icon={
                                            lockIsMine ? "lock-01" : "lock-01"
                                        }
                                    />
                                    <span className="number">
                                        Lock {mmss(lockRemaining(selectedLock))}
                                    </span>
                                </div>
                            )}
                        </div>
                        {lockBlocksMe && (
                            <div className="warning">
                                Em revisao por {selectedLock.adminName}. Os
                                botoes estao desativados.
                            </div>
                        )}
                        <FileReader resource={selected} />
                        <div className="score-bar">
                            <span
                                style={{
                                    width: `${selected.score}%`,
                                    background: scoreColor(selected.score),
                                }}
                            />
                        </div>
                        <div className="score-readout">
                            <b>
                                <Icon icon="chart-bar" />
                                {selected.score}/100
                            </b>
                            <span>{scoreLabel(selected.score)}</span>
                        </div>
                        <div className="checks">
                            <Check
                                label="MIME valido"
                                icon="file-search"
                                ok={selected.checks?.mime}
                            />
                            <Check
                                label="Hash rejeitado"
                                icon="fingerprint"
                                ok={!selected.checks?.blacklisted}
                            />
                            <Check
                                label="Palavras-chave"
                                icon="key"
                                ok={!selected.detected?.length}
                            />
                            <Check
                                label="Extensao segura"
                                icon="file-export"
                                ok={selected.checks?.extension}
                            />
                        </div>
                        <div className="hash-box">
                            <Icon icon="fingerprint" />
                            <span>{selected.hash}</span>
                        </div>
                        <h3>Sinais detectados</h3>
                        <div className="chips">
                            {selected.detected?.length ? (
                                selected.detected.map((item) => (
                                    <span
                                        key={item.word}
                                        className={`severity ${item.severity}`}
                                    >
                                        <Icon
                                            icon={severityIcon(item.severity)}
                                        />
                                        {item.word} - {item.severity}
                                    </span>
                                ))
                            ) : (
                                <span className="muted">
                                    Nenhum sinal detectado.
                                </span>
                            )}
                        </div>
                        <div className="actions">
                            <button
                                className="ghost"
                                disabled={!lockIsMine}
                                onClick={renewLock}
                            >
                                <Icon icon="refresh" /> Renovar Lock
                            </button>
                            <button
                                className="ok"
                                disabled={!lockIsMine}
                                onClick={approve}
                            >
                                <Icon icon="checkmark-circle-01" /> Aprovar
                            </button>
                            <button
                                className="danger"
                                disabled={!lockIsMine}
                                onClick={reject}
                            >
                                <Icon icon="cancel-circle" /> Rejeitar
                            </button>
                        </div>
                    </>
                )}
            </div>
        </div>
    );
}

function RejectedHashes({
    hashes,
    blacklistHashes,
    resources,
    onAddHash,
    onUpdateHash,
    onRemoveHash,
}) {
    const [hash, setHash] = useState("");
    const [reason, setReason] = useState("");
    const [message, setMessage] = useState(null);
    const [savingHash, setSavingHash] = useState(false);
    const [adding, setAdding] = useState(false);
    const [editingHash, setEditingHash] = useState(null);

    const hashCounts = useMemo(() => {
        return resources.reduce((counts, resource) => {
            if (!resource.hash || resource.status !== "rejected") return counts;
            counts[resource.hash] = (counts[resource.hash] || 0) + 1;
            return counts;
        }, {});
    }, [resources]);

    const submitHash = async () => {
        if (!hash.trim()) return;
        setSavingHash(true);
        setMessage(null);

        try {
            if (editingHash) {
                await onUpdateHash(editingHash.id, hash.trim(), reason.trim());
            } else {
                await onAddHash(hash.trim(), reason.trim());
            }
            setMessage({
                type: "success",
                text: "Hash adicionada à blacklist.",
            });
            setHash("");
            setReason("");
            setEditingHash(null);
        } catch (error) {
            setMessage({ type: "error", text: error.message });
        } finally {
            setSavingHash(false);
        }
    };

    const removeHash = async (id) => {
        setMessage(null);
        try {
            await onRemoveHash(id);
            setMessage({
                type: "success",
                text: "Hash removida da blacklist.",
            });
        } catch (error) {
            setMessage({ type: "error", text: error.message });
        }
    };

    const openHashModal = (item = null) => {
        setEditingHash(item);
        setHash(item?.hash || "");
        setReason(item?.reason || "");
        setAdding(true);
    };

    return (
        <div className="blacklist-layout">
            <div className="panel">
                <h2>Hashes rejeitados</h2>
                <p className="muted block">
                    Pontuacao: hash rejeitado exacto soma pelo menos 70 pontos e
                    força score critico perto de 95/100; hash parcial soma 52.
                </p>
                {hashes.length ? (
                    <table>
                        <thead>
                            <tr>
                                <th>Hash</th>
                                <th>Recurso</th>
                                <th>Motivo</th>
                                <th>Data</th>
                                <th>Quem</th>
                            </tr>
                        </thead>
                        <tbody>
                            {hashes.map((item) => (
                                <tr key={`${item.id}-${item.hash}`}>
                                    <td className="hash-cell">{item.hash}</td>
                                    <td>{item.resource}</td>
                                    <td>{item.motivo}</td>
                                    <td>{readableDate(item.date)}</td>
                                    <td>{item.by}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <EmptyState text="Nenhum hash rejeitado nos recursos reais." />
                )}
            </div>

            <div className="panel">
                <div className="section-head">
                    <div>
                        <h2>Hashes da blacklist</h2>
                        <p>Hashes bloqueados para uploads futuros.</p>
                    </div>
                    <button onClick={() => openHashModal()}>
                        <Icon icon="add-circle" /> Adicionar Hash
                    </button>
                </div>
                {blacklistHashes.length ? (
                    <div className="hash-list">
                        {blacklistHashes.map((item) => (
                            <div key={item.id} className="hash-item">
                                <div>
                                    <span className="hash-cell">
                                        {item.hash}
                                    </span>
                                    <small>{item.reason || "Sem motivo"}</small>
                                    <small>
                                        {hashCounts[item.hash] || 0} uploads
                                        bloqueados
                                    </small>
                                    <small>
                                        Adicionado por:{" "}
                                        {item.created_by_name ||
                                            item.created_by}
                                    </small>
                                </div>
                                <div className="table-actions">
                                    <button
                                        className="ghost small icon-only"
                                        title="Editar hash"
                                        onClick={() => openHashModal(item)}
                                    >
                                        <Icon icon="edit-01" />
                                    </button>
                                    <button
                                        className="ghost small icon-only danger-text"
                                        title="Remover hash"
                                        onClick={() => removeHash(item.id)}
                                    >
                                        <Icon icon="delete-01" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <EmptyState text="Nenhum hash colocado na blacklist." />
                )}
                {false && (
                    <div className="panel-form">
                        <h3>Adicionar hash à blacklist</h3>
                        <input
                            value={hash}
                            onChange={(event) => setHash(event.target.value)}
                            placeholder="Hash de um ficheiro"
                        />
                        <textarea
                            value={reason}
                            onChange={(event) => setReason(event.target.value)}
                            placeholder="Motivo (opcional)"
                        />
                        <button
                            className="primary full"
                            disabled={savingHash || !hash.trim()}
                            onClick={submitHash}
                        >
                            {savingHash ? "Guardando..." : "Adicionar hash"}
                        </button>
                    </div>
                )}
            </div>

            {message && (
                <div className={`message ${message.type}`}>{message.text}</div>
            )}
            {adding && (
                <div
                    className="modal-backdrop"
                    onMouseDown={() => {
                        setAdding(false);
                        setEditingHash(null);
                    }}
                >
                    <div
                        className="modal"
                        onMouseDown={(event) => event.stopPropagation()}
                    >
                        <div className="modal-title">
                            <Icon icon="add-circle" />
                            <div>
                                <h2>
                                    {editingHash
                                        ? "Editar Hash"
                                        : "Adicionar Hash"}
                                </h2>
                                <p>Registe o hash e o motivo da rejeicao.</p>
                            </div>
                        </div>
                        <div className="form-grid">
                            <label>
                                Hash
                                <input
                                    value={hash}
                                    onChange={(event) =>
                                        setHash(event.target.value)
                                    }
                                    placeholder="Hash de um ficheiro"
                                />
                            </label>
                            <label>
                                Motivo
                                <textarea
                                    value={reason}
                                    onChange={(event) =>
                                        setReason(event.target.value)
                                    }
                                    placeholder="Motivo (opcional)"
                                />
                            </label>
                        </div>
                        <div className="modal-actions">
                            <button
                                className="ghost"
                                onClick={() => {
                                    setAdding(false);
                                    setEditingHash(null);
                                }}
                            >
                                <Icon icon="cancel-01" /> Cancelar
                            </button>
                            <button
                                className="primary"
                                disabled={savingHash || !hash.trim()}
                                onClick={async () => {
                                    await submitHash();
                                    setAdding(false);
                                    setEditingHash(null);
                                }}
                            >
                                <Icon icon="sent" />
                                {savingHash
                                    ? "Guardando..."
                                    : editingHash
                                      ? "Guardar Hash"
                                      : "Adicionar Hash"}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <div className="panel score-help">
                <h2>Pontuacao de risco</h2>
                <div className="score-help-grid">
                    <span><b>Baixo</b> +8 pontos</span>
                    <span><b>Médio</b> +16 pontos</span>
                    <span><b>Alto</b> +28 pontos</span>
                    <span><b>Crítico</b> +42 pontos</span>
                </div>
                <p className="muted block">
                    Ate 30 aprova automaticamente, 31-79 entra em revisao, 80+
                    fica critico/rejeitado.
                </p>
            </div>
        </div>
    );
}

function FileReader({ resource, large = false }) {
    const kind = fileKind(resource);
    const className = large ? "preview-box reader large" : "preview-box reader";

    if (kind === "pdf") {
        return (
            <div className={className}>
                {resource.preview_url ? (
                    <iframe
                        title={`Leitor PDF - ${resource.title}`}
                        src={resource.preview_url}
                    ></iframe>
                ) : (
                    <div className="reader-unavailable">
                        <Icon icon="doc-01" />
                        <span>
                            Pre-visualizacao indisponivel para este PDF.
                        </span>
                    </div>
                )}
            </div>
        );
    }

    if (kind === "video") {
        return resource.preview_url ? (
            <div className={className}>
                <video controls src={resource.preview_url}></video>
            </div>
        ) : (
            <div className={`${className} file`}>
                <Icon icon="video-01" />
                <span>Pre-visualizacao indisponivel para este video.</span>
            </div>
        );
    }

    if (kind === "audio") {
        return (
            <div className={`${className} audio`}>
                <div className="audio-reader-head">
                    <Icon icon="music-note-01" />
                    <div>
                        <strong>{resource.title}</strong>
                        <span>
                            {resource.preview_url
                                ? "Audio do recurso"
                                : "Pre-visualizacao indisponivel para este audio."}
                        </span>
                    </div>
                </div>
                {resource.preview_url && (
                    <audio controls src={resource.preview_url}></audio>
                )}
            </div>
        );
    }

    if (kind === "epub" || kind === "docx" || kind === "book") {
        return (
            <div className={`${className} text-reader`}>
                <div className="reader-toolbar">
                    <Icon icon={kind === "docx" ? "doc-01" : "book-open-01"} />
                    <strong>
                        {kind === "docx"
                            ? "Conteudo extraido"
                            : "Texto extraido"}
                    </strong>
                </div>
                <div className="reader-text">
                    <p>
                        Este leitor mostra uma extraccao mock do recurso para
                        revisao. O conteudo real sera apresentado aqui quando o
                        pipeline de extraccao estiver ligado ao ficheiro.
                    </p>
                    <p>
                        Titulo: {resource.title}. Uploader:{" "}
                        {resource.uploader || "Sem uploader"}. Tipo:{" "}
                        {resource.format || resource.type}.
                    </p>
                    <p>
                        O moderador pode analisar esta area antes de aprovar ou
                        rejeitar o item na fila.
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className={`${className} file`}>
            <Icon icon="doc-01" />
            <span>Este formato nao tem leitor embutido.</span>
        </div>
    );
}

const PreviewPanel = FileReader;

function Keywords({
    blacklistWords,
    resources,
    onAddWord,
    onUpdateWord,
    onRemoveWord,
}) {
    const [word, setWord] = useState("");
    const [category, setCategory] = useState("");
    const [severity, setSeverity] = useState("high");
    const [message, setMessage] = useState(null);
    const [saving, setSaving] = useState(false);
    const [adding, setAdding] = useState(false);
    const [editingWord, setEditingWord] = useState(null);

    const hitsByKey = useMemo(() => {
        return resources.reduce((acc, resource) => {
            (resource.detected || []).forEach((hit) => {
                const key = `${String(hit.word).toLowerCase()}|${hit.severity}`;
                acc[key] = acc[key] || { hits: 0, resources: new Set() };
                acc[key].hits += 1;
                acc[key].resources.add(resource.title);
            });
            return acc;
        }, {});
    }, [resources]);

    const groupedKeywords = useMemo(() => {
        return blacklistWords.reduce((groups, item) => {
            const group = groups[item.category] || (groups[item.category] = []);
            const key = `${String(item.word).toLowerCase()}|${item.severity}`;
            const summary = hitsByKey[key] || { hits: 0, resources: new Set() };
            group.push({
                ...item,
                hits: summary.hits,
                resources: Array.from(summary.resources),
            });
            return groups;
        }, {});
    }, [blacklistWords, hitsByKey]);

    const categories = Object.entries(groupedKeywords).sort(([a], [b]) =>
        a.localeCompare(b),
    );

    const submitWord = async () => {
        const trimmed = word.trim();
        if (!trimmed) return;
        setSaving(true);
        setMessage(null);

        try {
            const payload = {
                word: trimmed,
                category: category.trim() || "Geral",
                severity,
            };
            if (editingWord) {
                await onUpdateWord(editingWord.id, payload);
            } else {
                await onAddWord(payload);
            }
            setMessage({
                type: "success",
                text: editingWord
                    ? "Palavra atualizada na blacklist."
                    : "Palavra adicionada a blacklist.",
            });
            setWord("");
            setCategory("");
            setSeverity("high");
            setEditingWord(null);
        } catch (error) {
            setMessage({ type: "error", text: error.message });
        } finally {
            setSaving(false);
        }
    };

    const removeWord = async (id) => {
        setMessage(null);
        try {
            await onRemoveWord(id);
            setMessage({
                type: "success",
                text: "Palavra removida da blacklist.",
            });
        } catch (error) {
            setMessage({ type: "error", text: error.message });
        }
    };

    const openWordModal = (item = null) => {
        setEditingWord(item);
        setWord(item?.word || "");
        setCategory(item?.category || "");
        setSeverity(item?.severity || "high");
        setAdding(true);
    };

    return (
        <div className="keywords-layout">
            <div className="panel">
                <div className="section-head">
                    <div>
                        <h2>Gerir palavras perigosas</h2>
                        <p>Palavras-chave usadas no score automatico.</p>
                    </div>
                    <button onClick={() => openWordModal()}>
                        <Icon icon="add-circle" /> Adicionar Keyword
                    </button>
                </div>
                <div className="score-help-grid compact">
                    <span><b>Baixo</b> +8</span>
                    <span><b>Médio</b> +16</span>
                    <span><b>Alto</b> +28</span>
                    <span><b>Crítico</b> +42</span>
                </div>
                {false && (
                    <div className="panel-form">
                        <h3>Adicionar palavra</h3>
                        <input
                            value={word}
                            onChange={(event) => setWord(event.target.value)}
                            placeholder="Palavra proibida"
                        />
                        <input
                            value={category}
                            onChange={(event) =>
                                setCategory(event.target.value)
                            }
                            placeholder="Categoria"
                        />
                        <select
                            value={severity}
                            onChange={(event) =>
                                setSeverity(event.target.value)
                            }
                        >
                            <option value="low">Baixa</option>
                            <option value="medium">Média</option>
                            <option value="high">Alta</option>
                            <option value="critical">Crítica</option>
                        </select>
                        <button
                            className="primary full"
                            disabled={
                                saving || !word.trim() || !category.trim()
                            }
                            onClick={submitWord}
                        >
                            {saving ? "Guardando..." : "Adicionar palavra"}
                        </button>
                    </div>
                )}
            </div>

            {categories.length ? (
                categories.map(([categoryName, items]) => (
                    <div className="panel" key={categoryName}>
                        <div className="section-head">
                            <h2>{categoryName}</h2>
                            <small>{items.length} palavra(s)</small>
                        </div>
                        <div className="keyword-grid">
                            {items.map((item) => (
                                <div className="keyword-card" key={item.id}>
                                    <div className="keyword-card-header">
                                        <strong>{item.word}</strong>
                                        <div className="table-actions">
                                            <button
                                                className="ghost small icon-only"
                                                title="Editar palavra"
                                                onClick={() =>
                                                    openWordModal(item)
                                                }
                                            >
                                                <Icon icon="edit-01" />
                                            </button>
                                            <button
                                                className="ghost small icon-only danger-text"
                                                title="Remover palavra"
                                                onClick={() =>
                                                    removeWord(item.id)
                                                }
                                            >
                                                <Icon icon="delete-01" />
                                            </button>
                                        </div>
                                    </div>
                                    <div className="keyword-row">
                                        <b
                                            className={`severity ${item.severity}`}
                                        >
                                            {severityLabel(item.severity)}
                                        </b>
                                        <small>{item.hits} deteccoes</small>
                                    </div>
                                    <p className="muted block">
                                        {item.resources.length
                                            ? item.resources.join(", ")
                                            : "Ainda não detetado em recursos reais."}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                ))
            ) : (
                <div className="panel">
                    <EmptyState text="Nenhuma palavra de blacklist encontrada." />
                </div>
            )}

            {message && (
                <div className={`message ${message.type}`}>{message.text}</div>
            )}
            {adding && (
                <div
                    className="modal-backdrop"
                    onMouseDown={() => {
                        setAdding(false);
                        setEditingWord(null);
                    }}
                >
                    <div
                        className="modal"
                        onMouseDown={(event) => event.stopPropagation()}
                    >
                        <div className="modal-title">
                            <Icon icon="key" />
                            <div>
                                <h2>
                                    {editingWord
                                        ? "Editar Keyword"
                                        : "Adicionar Keyword"}
                                </h2>
                                <p>Defina categoria e severidade da palavra.</p>
                            </div>
                        </div>
                        <div className="form-grid">
                            <label>
                                Keyword
                                <input
                                    value={word}
                                    onChange={(event) =>
                                        setWord(event.target.value)
                                    }
                                    placeholder="Palavra proibida"
                                />
                            </label>
                            <label>
                                Categoria
                                <input
                                    value={category}
                                    onChange={(event) =>
                                        setCategory(event.target.value)
                                    }
                                    placeholder="Categoria"
                                />
                            </label>
                            <label>
                                Severidade
                                <select
                                    value={severity}
                                    onChange={(event) =>
                                        setSeverity(event.target.value)
                                    }
                                >
                                    <option value="low">Baixa</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                    <option value="critical">Critica</option>
                                </select>
                            </label>
                        </div>
                        <div className="modal-actions">
                            <button
                                className="ghost"
                                onClick={() => {
                                    setAdding(false);
                                    setEditingWord(null);
                                }}
                            >
                                <Icon icon="cancel-01" /> Cancelar
                            </button>
                            <button
                                className="primary"
                                disabled={
                                    saving || !word.trim() || !category.trim()
                                }
                                onClick={async () => {
                                    await submitWord();
                                    setAdding(false);
                                    setEditingWord(null);
                                }}
                            >
                                <Icon icon="sent" />
                                {saving
                                    ? "Guardando..."
                                    : editingWord
                                      ? "Guardar Keyword"
                                      : "Adicionar Keyword"}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

function Metric({ label, value, tone = "" }) {
    return (
        <div className={`metric ${tone}`}>
            <span>{label}</span>
            <strong className="number">{value}</strong>
        </div>
    );
}

function Icon({ icon, big = false }) {
    const name = iconName(icon);
    const paths = {
        "home-01": '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
        activity: '<path d="M3 12h4l2-6 4 12 2-6h6"/>',
        library: '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v17H6.5A2.5 2.5 0 0 0 4 22V5.5Z"/><path d="M20 5.5A2.5 2.5 0 0 0 17.5 3H13v17h4.5A2.5 2.5 0 0 1 20 22V5.5Z"/>',
        "time-quarter": '<circle cx="12" cy="12" r="9"/><path d="M12 7v5h4"/>',
        "shield-blocked": '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 9 6 6"/><path d="m15 9-6 6"/>',
        key: '<circle cx="7.5" cy="14.5" r="3.5"/><path d="M10 12 21 1"/><path d="m16 6 3 3"/><path d="m13 9 3 3"/>',
        "user-group": '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        "notification-01": '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        "logout-01": '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        refresh: '<path d="M21 12a9 9 0 0 1-15.5 6.2"/><path d="M3 12A9 9 0 0 1 18.5 5.8"/><path d="M18 2v4h4"/><path d="M6 22v-4H2"/>',
        "cancel-circle": '<circle cx="12" cy="12" r="9"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
        "cancel-01": '<path d="m18 6-12 12"/><path d="m6 6 12 12"/>',
        "lock-01": '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        "unlock-01": '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 7.5-2"/>',
        "user-check-01": '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>',
        "add-circle": '<circle cx="12" cy="12" r="9"/><path d="M12 8v8"/><path d="M8 12h8"/>',
        "edit-01": '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>',
        "delete-01": '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 15H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>',
        sent: '<path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/>',
        "search-01": '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        filter: '<path d="M3 5h18"/><path d="M6 12h12"/><path d="M10 19h4"/>',
        "pdf-01": '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M9 16h1.5a1.5 1.5 0 0 0 0-3H9v5"/><path d="M14 13v5"/><path d="M16 13h3"/><path d="M16 16h2"/>',
        "video-01": '<rect x="3" y="6" width="14" height="12" rx="2"/><path d="m17 10 4-2v8l-4-2"/>',
        "music-note-01": '<path d="M9 18V5l10-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="16" cy="16" r="3"/>',
        "book-open-01": '<path d="M2 5a3 3 0 0 1 3-3h6v18H5a3 3 0 0 0-3 3V5Z"/><path d="M22 5a3 3 0 0 0-3-3h-6v18h6a3 3 0 0 1 3 3V5Z"/>',
        "doc-01": '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/>',
        "chart-bar": '<path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20H2"/>',
        fingerprint: '<path d="M12 11a3 3 0 0 0-3 3c0 2-1 4-2 5"/><path d="M12 11a3 3 0 0 1 3 3c0 1.5.5 3.5 2 5"/><path d="M6 14a6 6 0 0 1 12 0"/><path d="M4 10a9 9 0 0 1 16 0"/><path d="M12 14v2"/><path d="M10 22c1-1 2-3 2-6"/>',
        "checkmark-circle-01": '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        "alert-diamond": '<path d="m12 2 10 10-10 10L2 12 12 2Z"/><path d="M12 8v5"/><path d="M12 16h.01"/>',
        "alert-circle": '<circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16h.01"/>',
        "information-circle": '<circle cx="12" cy="12" r="9"/><path d="M12 11v6"/><path d="M12 7h.01"/>',
        "checkmark-badge-01": '<path d="M12 2 15 5l4-.5.5 4 3 3.5-3 3.5-.5 4-4-.5-3 3-3-3-4 .5-.5-4-3-3.5 3-3.5.5-4 4 .5 3-3Z"/><path d="m8 12 3 3 5-6"/>',
    };
    const fallback = '<circle cx="12" cy="12" r="9"/><path d="M12 8v8"/><path d="M8 12h8"/>';

    return (
        <svg
            className={big ? "icon big-icon" : "icon"}
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.8"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
            style={{
                width: big ? "52px" : "18px",
                height: big ? "52px" : "18px",
                color: "currentColor",
                flex: "none",
                margin: big ? "auto" : undefined,
            }}
            dangerouslySetInnerHTML={{ __html: paths[name] || fallback }}
        ></svg>
    );
}

function StatusBadge({ status }) {
    return <span className={`status ${status}`}>{statusLabel(status)}</span>;
}

function ScorePill({ score }) {
    return (
        <span
            className="score-pill number"
            style={{ color: scoreColor(score), borderColor: scoreColor(score) }}
        >
            {score}
        </span>
    );
}

function Check({ label, ok, icon = "checkmark-badge-01" }) {
    return (
        <div className={ok ? "check ok" : "check bad"}>
            <Icon icon={icon} />
            <b>{ok ? "OK" : "ALERTA"}</b>
            <span>{label}</span>
        </div>
    );
}

function EmptyState({ text }) {
    return <div className="empty">{text}</div>;
}

function LogLine({ log }) {
    return (
        <div className="log-line">
            <span>{readableDate(log.timestamp)}</span>
            <b>{log.action}</b>
            <small>
                {log.resource} - {log.result}
            </small>
        </div>
    );
}

function AppStyles() {
    return (
        <style>{`
      :root{--color-primary:#FE6807;--color-primary-hover:#e55d05;--color-secondary:#9b6b3f;--color-tertiary:#ffffff;--color-bg:#fbfaf7;--color-surface:#ffffff;--color-border:#eadfce;--color-text:#241b14;--color-text-muted:#66594d;--color-approved:#16a34a;--color-review:#d97706;--color-rejected:#dc2626}
      @media (prefers-color-scheme: dark){:root{--color-primary:#FE6807;--color-primary-hover:#ffad77;--color-secondary:#ffb07a;--color-tertiary:#0d0b09;--color-bg:#080706;--color-surface:#090909;--color-border:#27211a;--color-text:#f7efe7;--color-text-muted:#bcae9f}}
      *{box-sizing:border-box}body{margin:0;background:var(--color-bg);color:var(--color-text);font-family:"Golos",Arial,sans-serif}button,input,select,textarea{font:inherit}button{cursor:pointer}.number,.hash-cell,.hash-box,.score-pill,.metric strong,.summary-row b,.lock-timer,h1,h2,h3{font-family:"Space Grotesk","Golos",sans-serif}
      .app-shell{display:grid;grid-template-columns:292px 1fr;min-height:100vh;background:var(--color-bg)}
      .sidebar{position:sticky;top:0;height:100vh;border-right:1px solid var(--color-border);background:var(--color-surface);padding:22px;display:flex;flex-direction:column;gap:18px;box-shadow:18px 0 38px rgba(54,39,25,.06)}
      .brand,.admin-card{display:flex;align-items:center;gap:12px}.brand strong{display:block;font-size:20px}.brand span,.admin-card span,.topbar p,.topbar small,.muted{color:var(--color-text-muted);font-size:12px}.brand-logo{width:auto;height:42px;object-fit:contain}
      .avatar{display:grid;place-items:center;width:44px;height:44px;border-radius:50%;background:var(--color-bg);border:1px solid var(--color-border);color:var(--color-primary);font-weight:800}.avatar.mini{width:28px;height:28px;font-size:10px}.admin-name{display:inline-flex;align-items:center;gap:9px}.admin-name img{width:28px;height:28px;border-radius:50%;object-fit:cover}.admin-card{border:1px solid var(--color-border);background:var(--color-bg);border-radius:12px;padding:12px}.admin-card strong{display:block;font-size:13px}
      nav{display:grid;gap:0;overflow:auto}.nav-group{display:grid;gap:7px;border-top:1px solid var(--color-border);padding-top:14px;margin-top:4px}.nav-group small{color:var(--color-secondary);text-transform:uppercase;letter-spacing:.12em;font-size:10px;padding-left:12px;font-weight:800}
      nav button,.logout,.primary-link{min-height:42px;border:1px solid transparent;border-radius:10px;background:transparent;color:var(--color-text-muted);text-align:left;padding:0 12px;display:flex;justify-content:space-between;align-items:center;text-decoration:none}nav button span,button .icon,.logout .icon,.primary-link .icon,.file-title,.detail-meta span{display:inline-flex;align-items:center;gap:9px}.icon{width:18px;height:18px;color:currentColor;flex:none}.big-icon{width:52px;height:52px;margin:auto;color:var(--color-primary)}
      nav button.active,nav button:hover,.logout:hover{border-color:var(--color-border);background:var(--color-bg);color:var(--color-primary)}nav b{background:var(--color-secondary);color:var(--color-text);border-radius:99px;padding:2px 8px;font-size:11px}.logout{margin-top:auto;width:100%;justify-content:flex-start;gap:8px}.primary-link{justify-content:center;background:var(--color-primary);color:var(--color-tertiary);font-weight:800}
      .main{padding:28px;min-width:0}.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;gap:16px}.topbar h1{margin:4px 0 0;font-size:30px}.topbar-actions{display:flex;gap:10px}.icon-button{width:42px;justify-content:center;padding:0}
      .panel,.metric{border:1px solid var(--color-border);background:var(--color-surface);border-radius:14px;padding:18px;box-shadow:0 18px 50px rgba(54,39,25,.08)}.panel h2{margin:0 0 14px;font-size:18px}.panel h3{font-size:14px;margin-top:18px;color:var(--color-secondary)}.panel p{line-height:1.7;color:var(--color-text-muted)}
      .grid-section{display:grid;grid-template-columns:1.4fr .7fr;gap:16px}.wide{grid-column:span 1}.cards{display:grid;gap:12px}.cards.six{grid-template-columns:repeat(3,minmax(0,1fr));grid-column:1/-1}.metric span{color:var(--color-text-muted);font-size:12px}.metric strong{display:block;margin-top:12px;font-size:32px}.metric.ok strong{color:var(--color-approved)}.metric.warn strong{color:var(--color-review)}.metric.bad strong{color:var(--color-rejected)}
      .chart{height:230px;display:flex;align-items:flex-end;gap:18px;padding-top:20px}.bar-group{flex:1;display:grid;gap:10px;text-align:center;color:var(--color-text-muted);font-size:11px}.bars{height:170px;display:flex;align-items:flex-end;justify-content:center;gap:5px}.bars i{display:block;width:18px;border-radius:8px 8px 0 0}
      .summary-row{display:flex;justify-content:space-between;gap:14px;border-bottom:1px solid var(--color-border);padding:12px 0}.summary-row b{word-break:break-word;text-align:right}.full{width:100%;margin-top:14px}.feed{grid-column:1/-1}.log-line{display:grid;grid-template-columns:180px 1fr 1.4fr;gap:12px;border-top:1px solid var(--color-border);padding:10px 0}.log-line span,.log-line small{color:var(--color-text-muted)}.table-actions{display:flex;gap:8px;flex-wrap:wrap}.actions.inline{margin-top:0}
      .filters,.inline-form{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}.search-field{display:flex;align-items:center;gap:8px;border:1px solid var(--color-border);background:var(--color-surface);border-radius:10px;padding:0 12px}.search-field input{border:0;background:transparent;padding:0}input,select,textarea{min-height:42px;border:1px solid var(--color-border);background:var(--color-surface);color:var(--color-text);border-radius:10px;padding:0 12px;outline:none}textarea{min-height:120px;padding:12px;resize:vertical}input:focus,select:focus,textarea:focus{border-color:var(--color-primary);box-shadow:0 0 0 3px rgba(254,104,7,.12)}
      button{border:0;border-radius:10px;background:var(--color-primary);color:var(--color-tertiary);min-height:42px;padding:0 14px;font-weight:800;display:inline-flex;align-items:center;gap:8px}button:hover{background:var(--color-primary-hover)}button:disabled{opacity:.45;cursor:not-allowed}.ghost{border:1px solid var(--color-border);background:var(--color-surface);color:var(--color-primary)}.ghost:hover{background:var(--color-bg)}button.ok{background:var(--color-approved)}button.danger{background:var(--color-rejected)}.danger-text{color:var(--color-rejected)}.small{min-height:34px;padding:0 10px;font-size:12px}.icon-only{min-height:34px;width:34px;padding:0;justify-content:center;flex-shrink:0}.warning,.error{border:1px solid color-mix(in srgb,var(--color-review),transparent 60%);background:var(--color-bg);color:var(--color-review);border-radius:12px;padding:12px;margin:12px 0}.error{border-color:color-mix(in srgb,var(--color-rejected),transparent 60%);color:var(--color-rejected)}
      table{width:100%;border-collapse:collapse}th,td{border-bottom:1px solid var(--color-border);padding:12px;text-align:left;font-size:13px;vertical-align:top}th{color:var(--color-primary);font-size:11px;text-transform:uppercase}.clickable-row{cursor:pointer}.clickable-row:hover{background:var(--color-bg)}.hash-cell,.hash-box{font-size:11px;word-break:break-all;color:var(--color-text-muted)}.hash-box{border:1px solid var(--color-border);background:var(--color-bg);border-radius:12px;padding:12px;margin:14px 0;display:flex;gap:10px;align-items:flex-start}.hash-box span{min-width:0}
      .status,.severity,.score-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;border:1px solid var(--color-border);padding:4px 9px;font-size:11px;font-weight:900}.status.approved{color:var(--color-approved);border-color:var(--color-approved)}.status.rejected{color:var(--color-rejected);border-color:var(--color-rejected)}.status.review{color:var(--color-review);border-color:var(--color-review)}.severity.low{color:var(--color-approved);border-color:var(--color-approved)}.severity.medium{color:var(--color-review);border-color:var(--color-review)}.severity.high,.severity.critical{color:var(--color-rejected);border-color:var(--color-rejected)}
      .queue-layout,.guide-grid{display:grid;grid-template-columns:minmax(320px,.8fr) 1.2fr;gap:16px}.queue-list{display:grid;gap:10px}.queue-item{display:grid;gap:7px;text-align:left;border:1px solid var(--color-border);background:var(--color-surface);color:var(--color-text);border-radius:12px;padding:14px;height:auto}.queue-item.active,.queue-item:hover{border-color:var(--color-primary);background:var(--color-bg)}.queue-item span,.queue-item small,.queue-item em{color:var(--color-text-muted);font-style:normal;font-size:12px}
      .detail-head{display:flex;justify-content:space-between;gap:16px}.detail-head p{color:var(--color-text-muted)}.detail-meta{display:flex;gap:12px;flex-wrap:wrap;color:var(--color-text-muted);font-size:12px}.lock-timer{border:1px solid var(--color-border);border-radius:12px;padding:12px;color:var(--color-primary);height:max-content;display:flex;gap:8px;align-items:center}.score-bar{height:12px;background:var(--color-bg);border-radius:99px;overflow:hidden}.score-bar span{display:block;height:100%;border-radius:99px;transition:width .25s ease}.score-readout{display:flex;justify-content:space-between;margin:10px 0 16px}.score-readout b{display:inline-flex;align-items:center;gap:8px}.checks{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.check{border:1px solid var(--color-border);border-radius:12px;padding:12px;display:grid;gap:4px}.check b{display:block}.check.ok b{color:var(--color-approved)}.check.bad b{color:var(--color-rejected)}.chips{display:flex;gap:8px;flex-wrap:wrap}.actions{display:flex;gap:10px;margin-top:18px;flex-wrap:wrap}
      .preview-box{margin:14px 0;border:1px solid var(--color-border);background:var(--color-surface);border-radius:12px;overflow:hidden}.preview-box iframe{width:100%;height:600px;border:0;background:var(--color-tertiary)}.preview-box.large iframe{height:min(72vh,720px)}.preview-box video{display:block;width:100%;max-height:520px;background:var(--color-text)}.preview-box.audio{padding:16px}.preview-box audio{width:100%}.preview-box.file,.reader-unavailable{display:flex;align-items:center;gap:10px;padding:16px}.reader-unavailable{min-height:180px;justify-content:center;color:var(--color-text-muted)}.audio-reader-head,.reader-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:12px}.audio-reader-head span{display:block;color:var(--color-text-muted);font-size:12px}.text-reader{padding:0}.reader-toolbar{border-bottom:1px solid var(--color-border);padding:14px 16px;margin:0}.reader-text{max-height:420px;overflow:auto;padding:16px;line-height:1.8;color:var(--color-text-muted)}
      .keywords-layout{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.keyword-row{display:flex;gap:10px;align-items:center}.keyword-row small{color:var(--color-text-muted)}.block{display:block;margin-top:14px}.blacklist-layout{display:grid;grid-template-columns:1fr 1fr;gap:20px}.blacklist-layout .panel{min-width:0}.score-help{grid-column:1/-1}.score-help-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.score-help-grid span{border:1px solid var(--color-border);background:var(--color-bg);border-radius:10px;padding:10px;font-size:12px}.score-help-grid b{display:block;color:var(--color-primary)}.keyword-list{list-style:none;padding:0;margin:0;display:grid;gap:10px}.keyword-list li,.keyword-card,.hash-item{border:1px solid var(--color-border);border-radius:14px;background:var(--color-bg)}.keyword-list li{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 14px}.keyword-list li div{display:flex;flex-direction:column;gap:4px}.keyword-badges{display:flex;gap:10px;align-items:center;margin-top:8px}.keyword-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.keyword-card{padding:18px;display:flex;flex-direction:column;gap:12px}.keyword-card-header{display:flex;justify-content:space-between;align-items:center;gap:12px}.hash-list{display:grid;gap:12px}.hash-item{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:14px}.hash-item small{display:block;color:var(--color-text-muted);margin-top:4px}
      .section-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:16px}.section-head h2{margin:0 0 6px}.section-head p,.modal-title p{margin:0;color:var(--color-text-muted);font-size:12px}.empty{border:1px dashed var(--color-border);border-radius:14px;padding:28px;text-align:center;color:var(--color-text-muted)}
      .modal-backdrop{position:fixed;inset:0;background:rgba(5,9,26,.72);display:grid;place-items:center;z-index:20;padding:16px}.modal{width:min(520px,calc(100vw - 32px));max-height:calc(100vh - 32px);overflow:auto;border:1px solid var(--color-border);background:var(--color-surface);border-radius:16px;padding:20px}.modal textarea{width:100%;margin:12px 0}.modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:16px}.reader-modal{width:min(980px,calc(100vw - 32px))}
      .user-modal{width:min(640px,calc(100vw - 32px))}.modal-title{display:flex;align-items:flex-start;gap:12px;margin-bottom:16px}.modal-title .icon{width:28px;height:28px;color:var(--color-primary)}.form-grid{display:grid;gap:12px}.form-grid label{display:grid;gap:7px;color:var(--color-primary);font-size:12px;font-weight:700}.confirm-box,.report{border:1px solid var(--color-border);border-radius:14px;padding:16px;background:var(--color-bg)}.report.ok{border-color:color-mix(in srgb,var(--color-approved),transparent 55%)}.report.bad{border-color:color-mix(in srgb,var(--color-rejected),transparent 55%)}.report pre{white-space:pre-wrap;background:var(--color-surface);border-radius:12px;padding:12px;color:var(--color-text-muted)}
      .flow{display:grid;gap:12px;margin-top:18px}.flow-step{display:grid;grid-template-columns:40px 1fr;gap:12px;border:1px solid var(--color-border);border-radius:12px;padding:12px}.flow-step b{display:grid;place-items:center;width:32px;height:32px;border-radius:50%;background:var(--color-primary);color:var(--color-tertiary);font-family:"Orbitron",monospace}.flow-step strong{display:block}.flow-step span{display:block;margin-top:4px;color:var(--color-text-muted);line-height:1.6}
      .login-page{min-height:100vh;display:grid;place-items:center;background:var(--color-bg)}.login-panel{width:min(440px,calc(100vw - 32px));border:1px solid var(--color-border);background:var(--color-surface);border-radius:18px;padding:28px;display:grid;gap:14px;text-align:center}.login-panel small,.login-panel p{color:var(--color-text-muted);line-height:1.6}
      @media(max-width:980px){.app-shell{grid-template-columns:1fr}.sidebar{position:relative;height:auto}.grid-section,.queue-layout,.guide-grid,.keywords-layout,.blacklist-layout{grid-template-columns:1fr}.cards.six{grid-template-columns:repeat(2,minmax(0,1fr))}.log-line{grid-template-columns:1fr}.topbar,.section-head{align-items:flex-start;gap:12px;flex-direction:column}.topbar-actions{width:100%;justify-content:flex-start}}
    `}</style>
    );
}

ReactDOM.createRoot(document.getElementById("vutivi-moderation-root")).render(
    <App />,
);
