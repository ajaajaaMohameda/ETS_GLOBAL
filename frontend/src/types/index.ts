// frontend/src/types/index.ts

export interface User {
  id: string;
  name: string;
  email: string;
}

export interface Session {
  id: string;
  language: string;
  startsAt: string;
  location: string;
  capacity: number;
}

export interface Reservation {
  id: string;
  sessionId: string;
  language: string;
  location: string;
  reservedAt: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  pagination: {
    total: number;
    page: number;
    limit: number;
    pages: number;
  };
}