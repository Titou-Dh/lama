"""
Event Search Indexation with TF-IDF Algorithm

This script implements TF-IDF (Term Frequency-Inverse Document Frequency)
algorithm to search for events based on relevance to a query.

Usage:
    python main.py <query> <json_data>

    Where:
    <query> - The search query text
    <json_data> - JSON string containing events data from the database
"""

import sys
import json
import math
import re
from collections import Counter, defaultdict


class TFIDFSearchEngine:
    def __init__(self, events):
        self.events = events
        self.documents = []
        self.document_freq = defaultdict(int)
        self.tokenize_events()
        self.calculate_document_frequencies()

    def tokenize_events(self):
        """Extract and tokenize searchable text from each event"""
        for event in self.events:
            searchable_text = " ".join(
                [
                    event.get("title", ""),
                    event.get("description", ""),
                    event.get("location", ""),
                    event.get("category_name", ""),
                    event.get("organizer_name", ""),
                ]
            ).lower()

            tokens = re.findall(r"\b\w+\b", searchable_text)

            self.documents.append(
                {
                    "event_id": event["id"],
                    "tokens": tokens,
                    "token_freq": Counter(tokens),
                }
            )

    def calculate_document_frequencies(self):
        """Calculate how many documents contain each term"""
        for doc in self.documents:
            for term in set(doc["tokens"]):
                self.document_freq[term] += 1

    def search(self, query):
        query_terms = re.findall(r"\b\w+\b", query.lower())
        query_term_freq = Counter(query_terms)

        N = len(self.documents)

        scores = []
        for doc in self.documents:
            score = 0
            for term, term_freq in query_term_freq.items():
                if term in doc["token_freq"]:
                    tf = doc["token_freq"][term]

                    idf = math.log(N / (1 + self.document_freq.get(term, 0)))

                    score += tf * idf

            if score > 0:
                scores.append((doc["event_id"], score))

        scores.sort(key=lambda x: x[1], reverse=True)

        return [event_id for event_id, score in scores]


def main():
    """Main function to process command line arguments and perform search"""
    if len(sys.argv) != 3:
        print("Usage: python main.py <query> <json_data>", file=sys.stderr)
        sys.exit(1)

    query = sys.argv[1]
    json_data = sys.argv[2]

    try:
        events = json.loads(json_data)

        search_engine = TFIDFSearchEngine(events)

        results = search_engine.search(query)

        print(json.dumps({"results": results, "query": query, "total": len(results)}))

    except json.JSONDecodeError as e:
        print(f"Error parsing JSON data: {e}", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"Error during search: {e}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
