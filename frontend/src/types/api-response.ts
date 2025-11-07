export interface ApiResponse<T> {
  status: number;
  message: string | null;
  error: string | null;
  data: T;
}
