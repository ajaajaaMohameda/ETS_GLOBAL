import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  const token = request.cookies.get('jwt_token')?.value;
  const { pathname } = request.nextUrl;

  const authRoutes = ['/login', '/register'];
  const protectedRoutes = ['/reservations', '/profile'];

  const isAuthRoute = authRoutes.includes(pathname);
  const isProtectedRoute = protectedRoutes.some(route => pathname.startsWith(route));

  if (!token && isProtectedRoute) {
    return NextResponse.redirect(new URL('/login', request.url));
  }

  if (token && isAuthRoute) {
    return NextResponse.redirect(new URL('/reservations', request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: ['/reservations/:path*', '/profile/:path*', '/login', '/register'],
};