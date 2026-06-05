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
  session: Session;
  user: User;
  createdAt: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    total: number;
    page: number;
    limit: number;
    pages: number;
  };
}