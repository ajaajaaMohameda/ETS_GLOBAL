import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import SessionsPage from '@/app/sessions/page';
import { sessionService } from '@/services/session.service';
import { reservationService } from '@/services/reservation.service';

jest.mock('next/navigation', () => ({
  useRouter() {
    return {
      push: jest.fn(),
    };
  },
}));

jest.mock('@/services/session.service');
jest.mock('@/services/reservation.service');

const mockSessions = {
  data: [
    {
      id: '65abc123',
      language: 'TOEIC',
      startsAt: '2026-10-15T10:00:00Z',
      location: 'Centre Paris',
      capacity: 15,
    },
  ],
  pagination: {
    total: 1,
    page: 1,
    limit: 6,
    pages: 1,
  },
};

describe('Page des Sessions Disponibles', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('affiche le texte de chargement puis la liste des sessions', async () => {
    (sessionService.getSessions as jest.Mock).mockResolvedValue(mockSessions);

    render(<SessionsPage />);

    expect(screen.getByText(/Chargement des sessions.../i)).toBeInTheDocument();

    await waitFor(() => {
      expect(screen.getByText('TOEIC')).toBeInTheDocument();
    });

    expect(screen.getByText('15 places')).toBeInTheDocument();
    expect(screen.getByText('Centre Paris')).toBeInTheDocument();
  });

  it('affiche une erreur si le chargement échoue', async () => {
    (sessionService.getSessions as jest.Mock).mockRejectedValue(new Error('Network error'));

    render(<SessionsPage />);

    await waitFor(() => {
      expect(
          screen.getByText(/Erreur lors du chargement des sessions disponibles./i)
      ).toBeInTheDocument();
    });
  });

  it('appelle le service de réservation au clic sur le bouton', async () => {
    (sessionService.getSessions as jest.Mock).mockResolvedValue(mockSessions);
    (reservationService.createReservation as jest.Mock).mockResolvedValue({ success: true });

    render(<SessionsPage />);

    await waitFor(() => {
      expect(screen.getByText('TOEIC')).toBeInTheDocument();
    });

    const reserveButton = screen.getByRole('button', { name: /réserver cette session/i });
    fireEvent.click(reserveButton);

    expect(reserveButton).toBeDisabled();

    await waitFor(() => {
      expect(reservationService.createReservation).toHaveBeenCalledWith('65abc123');
    });
  });

  it('désactive le bouton si la session est complète', async () => {
    (sessionService.getSessions as jest.Mock).mockResolvedValue({
      ...mockSessions,
      data: [{ ...mockSessions.data[0], capacity: 0 }],
    });

    render(<SessionsPage />);

    await waitFor(() => {
      expect(screen.getByText('TOEIC')).toBeInTheDocument();
    });

    const completButton = screen.getByRole('button', { name: /complet/i });
    expect(completButton).toBeDisabled();
  });
});