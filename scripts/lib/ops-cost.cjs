/**
 * Telemetrie de couts vers Sassify Ops (pilotage), version CommonJS pour les scripts de contenu.
 * Mini-portage de ops/clients/ops-cost.ts (copie canonique, garder synchronise) : Anthropic uniquement.
 *
 * Variables (dans .env.local, sans effet si absentes) : OPS_URL et OPS_INGEST_KEY (= NUXT_INGEST_KEY cote ops).
 * Regle d'or : la telemetrie ne casse ni ne ralentit jamais un script : fire and forget, aucune erreur ne remonte,
 * timeout de 10 s, circuit breaker apres 5 echecs consecutifs (pause 5 min). Les prix sont calcules cote ops a partir des tokens.
 *
 * Usage : apres `const response = await client.messages.create(params)` :
 *   trackAnthropicResponse('home', response, params, { feature: 'article' });
 */

let consecutiveFailures = 0;
let pausedUntil = 0;

async function trackCost(event) {
  const url = process.env.OPS_URL;
  const key = process.env.OPS_INGEST_KEY;
  if (!url || !key || Date.now() < pausedUntil) {
    return;
  }

  try {
    const response = await fetch(`${url.replace(/\/$/, '')}/api/ingest/cost`, {
      method: 'POST',
      headers: { 'content-type': 'application/json', 'x-ops-key': key },
      body: JSON.stringify(event),
      signal: AbortSignal.timeout(10000)
    });
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    consecutiveFailures = 0;
  } catch {
    consecutiveFailures += 1;
    if (consecutiveFailures >= 5) {
      pausedUntil = Date.now() + 5 * 60 * 1000;
      consecutiveFailures = 0;
    }
  }
}

const obj = (value) => (value && typeof value === 'object' ? value : {});
const num = (value) => (typeof value === 'number' && Number.isFinite(value) && value >= 0 ? Math.round(value) : undefined);
const str = (value) => (typeof value === 'string' && value ? value : undefined);

/** Reponse Anthropic /v1/messages (SDK ou fetch). Les erreurs, les corps vides et le streaming sont ignores. */
function trackAnthropicResponse(project, message, params, meta) {
  try {
    const body = obj(message);
    if (!message || typeof message !== 'object' || body.type === 'error' || body.error !== undefined || obj(params).stream === true) {
      return;
    }
    const usage = obj(body.usage);
    const model = str(body.model) || str(obj(params).model) || 'unknown';
    const llm = Object.keys(usage).length === 0
      ? { model }
      : {
          model,
          inputTokens: num(usage.input_tokens),
          outputTokens: num(usage.output_tokens),
          cacheReadTokens: num(usage.cache_read_input_tokens),
          cacheWriteTokens: num(usage.cache_creation_input_tokens)
        };
    void trackCost({ project, provider: 'anthropic', llm, meta });
  } catch {
    // la telemetrie n'a jamais le droit de casser un script
  }
}

module.exports = { trackCost, trackAnthropicResponse };
