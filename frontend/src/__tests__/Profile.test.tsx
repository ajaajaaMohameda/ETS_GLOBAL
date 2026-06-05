import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import ProfilePage from '@/app/profile/page';
import { userService } from '@/services/user.service';

jest.mock('@/services/user.service');

const mockUser = {
  id: 'usr-1',
  name: 'John Doe',
  email: 'john@example.com'
};

describe('Page Profil', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('affiche les données de l\'utilisateur après le chargement', async () => {
    (userService.getCurrentUser as jest.Mock).mockResolvedValue(mockUser);

    render(<ProfilePage />);

    expect(screen.getByText(/Chargement de votre profil.../i)).toBeInTheDocument();

    await waitFor(() => {
      expect(screen.getByDisplayValue('John Doe')).toBeInTheDocument();
    });
    
    expect(screen.getByDisplayValue('john@example.com')).toBeInTheDocument();
  });

  it('permet de modifier et de sauvegarder le profil', async () => {
    (userService.getCurrentUser as jest.Mock).mockResolvedValue(mockUser);
    (userService.updateProfile as jest.Mock).mockResolvedValue({ ...mockUser, name: 'Jane Doe' });

    render(<ProfilePage />);

    await waitFor(() => {
      expect(screen.getByDisplayValue('John Doe')).toBeInTheDocument();
    });

    const nameInput = screen.getByLabelText(/Nom complet/i);
    fireEvent.change(nameInput, { target: { value: 'Jane Doe' } });

    const submitButton = screen.getByRole('button', { name: /Enregistrer/i });
    fireEvent.click(submitButton);

    await waitFor(() => {
      expect(userService.updateProfile).toHaveBeenCalledWith('Jane Doe', 'john@example.com');
    });

    expect(screen.getByText(/Vos informations ont été mises à jour avec succès./i)).toBeInTheDocument();
  });
});