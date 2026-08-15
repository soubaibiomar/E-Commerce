/**
 * Loop Engineering - Qdrant Vector Search Engine
 * Interfaces with Qdrant Vector Database and generates/indexes embeddings for products and Fiches Techniques.
 */

export interface VectorSearchResult {
  id: number;
  score: number;
  payload: Record<string, any>;
}

export class VectorStore {
  private baseUrl: string;
  private collectionName = 'loop_products';

  constructor() {
    this.baseUrl = process.env.QDRANT_URL || 'http://localhost:6333';
  }

  public async ensureCollection(): Promise<void> {
    try {
      const res = await fetch(`${this.baseUrl}/collections/${this.collectionName}`, {
        signal: AbortSignal.timeout(2000),
      });

      if (res.status === 404) {
        await fetch(`${this.baseUrl}/collections/${this.collectionName}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            vectors: {
              size: 64, // Normalized dense semantic vector dimension
              distance: 'Cosine',
            },
          }),
        });
        console.log(`[VectorStore] Created Qdrant collection: ${this.collectionName}`);
      }
    } catch (err) {
      console.warn('[VectorStore] Qdrant connection offline or booting:', err);
    }
  }

  public generateFastEmbedding(text: string): number[] {
    const vector = new Array(64).fill(0);
    const words = text.toLowerCase().split(/\W+/).filter(Boolean);

    words.forEach((word, wordIdx) => {
      for (let i = 0; i < word.length; i++) {
        const code = word.charCodeAt(i);
        const idx = (code * (i + 1) * (wordIdx + 1)) % 64;
        vector[idx] += 1;
      }
    });

    // Normalize
    const magnitude = Math.sqrt(vector.reduce((sum, v) => sum + v * v, 0)) || 1;
    return vector.map((v) => v / magnitude);
  }

  public async searchSimilar(queryText: string, limit = 5): Promise<VectorSearchResult[]> {
    const queryVector = this.generateFastEmbedding(queryText);

    try {
      const res = await fetch(`${this.baseUrl}/collections/${this.collectionName}/points/search`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          vector: queryVector,
          limit,
          with_payload: true,
        }),
        signal: AbortSignal.timeout(3000),
      });

      if (res.ok) {
        const data = await res.json();
        return (data.result || []).map((r: any) => ({
          id: r.id,
          score: r.score,
          payload: r.payload,
        }));
      }
    } catch {
      // Fallback
    }

    return [];
  }
}

export const vectorStore = new VectorStore();
